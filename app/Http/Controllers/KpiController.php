<?php
namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Models\Lead;
use App\Models\ScheduledPost;
use App\Models\KpiMetric;
use App\Models\AiProvider;

class KpiController extends Controller
{
    public function index()
    {
        // Content breakdown
        $contentByType = ContentItem::selectRaw('type, count(*) as count')
            ->groupBy('type')->pluck('count', 'type');

        $contentByStatus = ContentItem::selectRaw('status, count(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        $contentByProvider = ContentItem::selectRaw('ai_provider, count(*) as count')
            ->groupBy('ai_provider')->pluck('count', 'ai_provider');

        $contentByPillar = ContentItem::with('pillar')
            ->selectRaw('content_pillar_id, count(*) as count')
            ->groupBy('content_pillar_id')
            ->get()
            ->mapWithKeys(fn($i) => [
                ($i->pillar?->name ?? 'Unknown') => $i->count
            ]);

        // Lead pipeline
        $leadsByStatus = Lead::selectRaw('status, count(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        $leadsBySegment = Lead::selectRaw('segment, count(*) as count')
            ->groupBy('segment')->pluck('count', 'segment');

        $leadsBySource = Lead::selectRaw('source, count(*) as count')
            ->groupBy('source')->pluck('count', 'source');

        // Publishing stats
        $publishStats = [
            'total_scheduled' => ScheduledPost::count(),
            'published'       => ScheduledPost::where('status', 'published')->count(),
            'pending'         => ScheduledPost::where('status', 'pending')->count(),
            'failed'          => ScheduledPost::where('status', 'failed')->count(),
        ];

        // AI usage
        $aiProviders = AiProvider::all();

        // Conversion funnel
        $funnel = [
            'content_generated' => ContentItem::count(),
            'content_published' => ContentItem::where('status', 'published')->count(),
            'leads_total'       => Lead::count(),
            'leads_qualified'   => Lead::whereIn('status', ['qualified','meeting','closed'])->count(),
            'meetings'          => Lead::where('status', 'meeting')->count(),
            'closed'            => Lead::where('status', 'closed')->count(),
        ];

        // Weekly content generation (last 4 weeks)
        $weeklyContent = collect(range(3, 0))->map(function ($weeksAgo) {
            $start = now()->subWeeks($weeksAgo)->startOfWeek();
            $end   = now()->subWeeks($weeksAgo)->endOfWeek();
            return [
                'week'  => $start->format('M j'),
                'count' => ContentItem::whereBetween('created_at', [$start, $end])->count(),
            ];
        });

        return view('kpi', compact(
            'contentByType', 'contentByStatus', 'contentByProvider', 'contentByPillar',
            'leadsByStatus', 'leadsBySegment', 'leadsBySource',
            'publishStats', 'aiProviders', 'funnel', 'weeklyContent'
        ));
    }
}