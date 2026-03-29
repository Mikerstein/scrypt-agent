<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentItem;
use App\Models\Lead;
use App\Models\ScheduledPost;
use App\Models\KpiMetric;
use App\Services\AI\AIProviderFactory;
use Carbon\Carbon;

class GenerateWeeklyReport extends Command
{
    protected $signature   = 'scrypt:weekly-report {--provider=groq}';
    protected $description = 'Generate SCRYPT weekly marketing performance report';

    public function handle(): void
    {
        $this->info('📊 Generating SCRYPT Weekly Report...');
        $this->newLine();

        $weekStart = now()->startOfWeek();
        $weekEnd   = now()->endOfWeek();

        // Gather real data
        $contentGenerated  = ContentItem::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $contentApproved   = ContentItem::whereBetween('created_at', [$weekStart, $weekEnd])->where('status', 'approved')->count();
        $contentPublished  = ContentItem::whereBetween('created_at', [$weekStart, $weekEnd])->where('status', 'published')->count();
        $contentByType     = ContentItem::whereBetween('created_at', [$weekStart, $weekEnd])
                                ->selectRaw('type, count(*) as count')
                                ->groupBy('type')->pluck('count', 'type')->toArray();
        $newLeads          = Lead::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $qualifiedLeads    = Lead::where('status', 'qualified')->count();
        $meetings          = Lead::where('status', 'meeting')->count();
        $closedDeals       = Lead::where('status', 'closed')->count();
        $leadsBySegment    = Lead::selectRaw('segment, count(*) as count')
                                ->groupBy('segment')->pluck('count', 'segment')->toArray();
        $scheduledPosts    = ScheduledPost::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $publishedPosts    = ScheduledPost::where('status', 'published')
                                ->whereBetween('published_at', [$weekStart, $weekEnd])->count();
        $failedPosts       = ScheduledPost::where('status', 'failed')
                                ->whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $totalLeads        = Lead::count();

        // Display data table
        $this->info("📅 Week: {$weekStart->format('M j')} — {$weekEnd->format('M j, Y')}");
        $this->newLine();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Content generated',  $contentGenerated],
                ['Content approved',   $contentApproved],
                ['Content published',  $contentPublished],
                ['Posts scheduled',    $scheduledPosts],
                ['Posts live',         $publishedPosts],
                ['New leads',          $newLeads],
                ['Total leads',        $totalLeads],
                ['Qualified leads',    $qualifiedLeads],
                ['Meetings booked',    $meetings],
                ['Closed deals',       $closedDeals],
            ]
        );

        $this->newLine();
        $this->info('🤖 Generating AI analysis...');

        // Build data summary for AI
        $typeBreakdown    = collect($contentByType)->map(fn($c,$t) => "{$c} {$t}")->implode(', ');
        $segmentBreakdown = collect($leadsBySegment)->map(fn($c,$s) => "{$c} {$s}")->implode(', ');

        $prompt = "Generate a concise weekly marketing performance report for SCRYPT 
            (institutional crypto platform, scrypt.swiss) based on this data:

            CONTENT PERFORMANCE:
            - Generated this week: {$contentGenerated} pieces ({$typeBreakdown})
            - Approved: {$contentApproved} | Published: {$contentPublished}
            - Scheduled posts: {$scheduledPosts} | Live: {$publishedPosts} | Failed: {$failedPosts}

            LEAD PIPELINE:
            - New leads this week: {$newLeads}
            - Total pipeline: {$totalLeads} leads
            - Qualified: {$qualifiedLeads} | Meetings: {$meetings} | Closed: {$closedDeals}
            - Segments: {$segmentBreakdown}

            Provide:
            1. PERFORMANCE SUMMARY (3-4 sentences, data-driven)
            2. TOP 3 WINS this week
            3. TOP 3 PRIORITIES for next week
            4. ONE contrarian insight or risk to watch
            
            Format clearly with headers. Institutional tone. Be specific, not generic.
            Base recommendations on the actual numbers above.";

        try {
            $ai     = AIProviderFactory::make($this->option('provider'));
            $report = $ai->generate($prompt, 1200);

            $this->newLine();
            $this->line('─────────────────────────────────────────────────');
            $this->line($report);
            $this->line('─────────────────────────────────────────────────');

            // Save as KPI metric record
            KpiMetric::create([
                'metric_type'   => 'weekly_report',
                'platform'      => 'overall',
                'value'         => $contentGenerated + $newLeads,
                'recorded_date' => now()->toDateString(),
                'period'        => 'weekly',
            ]);

            $this->newLine();
            $this->info('✅ Weekly report complete. Saved to KPI metrics.');

        } catch (\Exception $e) {
            $this->error('❌ AI analysis failed: ' . $e->getMessage());
        }

        // Run optimization analysis
$this->newLine();
$this->info('🔧 Running optimization analysis...');

try {
    $optimizer = new \App\Services\Content\ContentOptimizationService();
    $result    = $optimizer->analysePerformance($this->option('provider'));

    if ($result['analysis']) {
        $this->newLine();
        $this->line('─────────────────────────────────────────');
        $this->line($result['analysis']);
        $this->line('─────────────────────────────────────────');

        $newRules = $optimizer->parseAndSaveRules($result['analysis']);
        $this->info("✅ {$newRules} new optimization rules saved to database.");
        $this->info('   These will improve next week\'s content generation automatically.');
    }
} catch (\Exception $e) {
    $this->error('Optimization analysis failed: ' . $e->getMessage());
}
    }
}