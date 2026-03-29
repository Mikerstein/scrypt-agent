<?php
namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Models\Lead;
use App\Models\KpiMetric;
use App\Models\ScheduledPost;
use App\Models\ContentPillar;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_content'   => ContentItem::count(),
            'draft_content'   => ContentItem::where('status', 'draft')->count(),
            'approved_content'=> ContentItem::where('status', 'approved')->count(),
            'published_content'=> ContentItem::where('status', 'published')->count(),
            'total_leads'     => Lead::count(),
            'new_leads'       => Lead::where('status', 'new')->count(),
            'qualified_leads' => Lead::where('status', 'qualified')->count(),
            'meetings'        => Lead::where('status', 'meeting')->count(),
        ];

        $recentContent = ContentItem::with('pillar')
            ->latest()
            ->take(5)
            ->get();

        $recentLeads = Lead::latest()->take(5)->get();

        $pillars = ContentPillar::where('is_active', true)->get();

        return view('dashboard', compact('stats', 'recentContent', 'recentLeads', 'pillars'));
    }
}