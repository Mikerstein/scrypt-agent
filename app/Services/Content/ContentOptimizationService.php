<?php
namespace App\Services\Content;

use App\Models\ContentItem;
use App\Models\ScheduledPost;
use App\Models\OptimizationRule;
use App\Models\KpiMetric;
use App\Services\AI\AIProviderFactory;
use Illuminate\Support\Facades\Log;

class ContentOptimizationService
{
    public function getActiveRules(string $platform = 'all'): string
    {
        $rules = OptimizationRule::active()
            ->forPlatform($platform)
            ->byWeight()
            ->get();

        if ($rules->isEmpty()) return '';

        $lines = $rules->map(fn($r) =>
            "- [{$r->rule_type}] {$r->instruction}"
        )->implode("\n");

        return "\n\nCONTENT OPTIMIZATION RULES (apply these based on past performance data):\n{$lines}\n";
    }

    public function analysePerformance(string $provider = 'groq'): array
    {
        // Gather performance data
        $totalContent    = ContentItem::count();
        $publishedCount  = ContentItem::where('status', 'published')->count();
        $approvedCount   = ContentItem::where('status', 'approved')->count();
        $rejectedCount   = ContentItem::where('status', 'rejected')->count();
        $totalLeads      = \App\Models\Lead::count();
        $qualifiedLeads  = \App\Models\Lead::whereIn('status', ['qualified','meeting','closed'])->count();
        $meetings        = \App\Models\Lead::where('status', 'meeting')->count();
        $closedDeals     = \App\Models\Lead::where('status', 'closed')->count();

        // Best performing content types
        $byType = ContentItem::where('status', 'published')
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Current active rules
        $currentRules = OptimizationRule::active()->byWeight()->get()
            ->map(fn($r) => "[{$r->rule_type}/{$r->platform}] {$r->instruction}")
            ->implode("\n");

        $conversionRate = $totalLeads > 0
            ? round(($qualifiedLeads / $totalLeads) * 100, 1)
            : 0;

        $approvalRate = $totalContent > 0
            ? round(($approvedCount / $totalContent) * 100, 1)
            : 0;

        $prompt = "You are the performance analyst for SCRYPT's AI marketing engine.

            CURRENT PERFORMANCE DATA:
            - Total content generated: {$totalContent}
            - Approval rate: {$approvalRate}% ({$approvedCount} approved, {$rejectedCount} rejected)
            - Published: {$publishedCount}
            - Total leads in pipeline: {$totalLeads}
            - Lead qualification rate: {$conversionRate}% ({$qualifiedLeads} qualified)
            - Meetings booked: {$meetings}
            - Deals closed: {$closedDeals}
            - Content by type: " . json_encode($byType) . "

            CURRENT OPTIMIZATION RULES ACTIVE:
            {$currentRules}

            TASK:
            Based on this performance data, generate 2-3 NEW specific optimization rules 
            that would improve content quality and lead conversion.

            For each new rule provide EXACTLY this format:
            RULE_TYPE: [hook|cta|tone|topic|format]
            PLATFORM: [linkedin|twitter|email|all]
            INSTRUCTION: [specific, actionable instruction in one sentence]
            EVIDENCE: [why this rule should improve performance based on the data above]
            CONFIDENCE: [0.00-1.00]

            Focus on what the data reveals. If approval rate is low, focus on quality.
            If leads are low, focus on CTAs and conversion. Be specific, not generic.";

        try {
            $ai       = AIProviderFactory::make($provider);
            $analysis = $ai->generate($prompt, 1000);

            return [
                'analysis'       => $analysis,
                'stats'          => compact(
                    'totalContent', 'publishedCount', 'approvedCount',
                    'rejectedCount', 'totalLeads', 'qualifiedLeads',
                    'meetings', 'closedDeals', 'conversionRate', 'approvalRate'
                ),
            ];
        } catch (\Exception $e) {
            Log::error('Optimization analysis failed: ' . $e->getMessage());
            return ['analysis' => null, 'stats' => []];
        }
    }

    public function parseAndSaveRules(string $analysis): int
    {
        $saved = 0;
        $lines = explode("\n", $analysis);

        $current = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                if ($this->isValidRule($current)) {
                    $this->saveRule($current);
                    $saved++;
                    $current = [];
                }
                continue;
            }

            if (str_starts_with($line, 'RULE_TYPE:'))  $current['rule_type']  = trim(substr($line, 10));
            if (str_starts_with($line, 'PLATFORM:'))   $current['platform']   = trim(substr($line, 9));
            if (str_starts_with($line, 'INSTRUCTION:'))$current['instruction']= trim(substr($line, 12));
            if (str_starts_with($line, 'EVIDENCE:'))   $current['evidence']   = trim(substr($line, 9));
            if (str_starts_with($line, 'CONFIDENCE:')) $current['confidence_score'] = (float) trim(substr($line, 11));
        }

        // Catch last rule if no trailing newline
        if ($this->isValidRule($current)) {
            $this->saveRule($current);
            $saved++;
        }

        return $saved;
    }

    private function isValidRule(array $rule): bool
    {
        return isset($rule['rule_type'], $rule['platform'], $rule['instruction'])
            && in_array($rule['rule_type'], ['hook','cta','tone','topic','format'])
            && !empty($rule['instruction']);
    }

    private function saveRule(array $rule): void
    {
        OptimizationRule::create([
            'rule_type'        => strtolower($rule['rule_type']),
            'platform'         => strtolower($rule['platform'] ?? 'all'),
            'instruction'      => $rule['instruction'],
            'weight'           => 2,
            'is_active'        => true,
            'source'           => 'ai_generated',
            'confidence_score' => $rule['confidence_score'] ?? 0.70,
            'evidence'         => $rule['evidence'] ?? null,
        ]);
    }
}