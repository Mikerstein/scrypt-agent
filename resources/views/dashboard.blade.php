@extends('layouts.app')
@section('title', 'Dashboard')

@section('header-actions')
    <a href="{{ route('content.index') }}" class="btn-primary">+ Generate Content</a>
@endsection

@section('content')

{{-- KPI strip --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @php
    $kpis = [
        ['label' => 'Total Content',    'value' => $stats['total_content'],     'sub' => $stats['draft_content'].' drafts',      'color' => '#FF6B35'],
        ['label' => 'Published',         'value' => $stats['published_content'], 'sub' => $stats['approved_content'].' approved', 'color' => '#3b82f6'],
        ['label' => 'Total Leads',       'value' => $stats['total_leads'],       'sub' => $stats['new_leads'].' new',             'color' => '#a855f7'],
        ['label' => 'Meetings Booked',   'value' => $stats['meetings'],          'sub' => $stats['qualified_leads'].' qualified', 'color' => '#22c55e'],
    ];
    @endphp

    @foreach($kpis as $kpi)
    <div class="card p-5">
        <div class="text-xs text-muted font-medium uppercase tracking-wider mb-3">{{ $kpi['label'] }}</div>
        <div class="text-3xl font-bold mb-1" style="color:{{ $kpi['color'] }}">{{ $kpi['value'] }}</div>
        <div class="text-xs text-muted">{{ $kpi['sub'] }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    {{-- Weekly content pillars --}}
    <div class="card p-5">
        <div class="text-sm font-semibold mb-4" style="color:var(--text-header)">Weekly Content Pillars</div>
        <div class="space-y-2">
            @foreach($pillars as $pillar)
            <div class="flex items-center justify-between py-2 border-b border-scrypt-border last:border-0">
                <div>
                    <div class="text-sm font-medium" style="color:var(--text-main)">{{ $pillar->name }}</div>
                    <div class="text-xs text-muted">{{ $pillar->day_of_week }}</div>
                </div>
                <span class="text-xs font-medium text-orange-400">{{ $pillar->day_of_week }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent leads --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="text-sm font-semibold" style="color:var(--text-header)">Recent Leads</div>
            <a href="{{ route('leads.index') }}" class="text-xs text-orange-400 hover:underline">View all</a>
        </div>
        <div class="space-y-3">
            @forelse($recentLeads as $lead)
            <a href="{{ route('leads.show', $lead) }}"
               class="flex items-center justify-between py-2 border-b border-scrypt-border last:border-0 hover:opacity-80 transition">
                <div>
                    <div class="text-sm font-medium" style="color:var(--text-main)">{{ $lead->name }}</div>
                    <div class="text-xs text-muted">{{ $lead->company }} · {{ str_replace('_',' ', $lead->segment) }}</div>
                </div>
                <span class="badge badge-{{ $lead->status }}">{{ $lead->status }}</span>
            </a>
            @empty
            <div class="text-sm text-muted">No leads yet.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Recent content --}}
<div class="card p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm font-semibold" style="color:var(--text-header)">Recent Content</div>
        <a href="{{ route('content.index') }}" class="text-xs text-orange-400 hover:underline">View all</a>
    </div>
    <div class="space-y-1">
        @forelse($recentContent as $item)
        <a href="{{ route('content.show', $item) }}"
           class="flex items-center justify-between py-3 border-b border-scrypt-border last:border-0 hover:opacity-80 transition">
            <div class="flex-1 min-w-0 pr-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-400">{{ $item->type }}</span>
                    <span class="text-xs text-muted">·</span>
                    <span class="text-xs text-muted">{{ $item->pillar?->name ?? 'No pillar' }}</span>
                    <span class="text-xs text-muted">·</span>
                    <span class="text-xs text-muted">{{ $item->ai_provider }}</span>
                </div>
                <div class="text-sm text-muted truncate">{{ Str::limit($item->content, 100) }}</div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="text-xs text-muted">{{ $item->created_at->diffForHumans() }}</span>
                <span class="badge badge-{{ $item->status }}">{{ $item->status }}</span>
            </div>
        </a>
        @empty
        <div class="text-sm text-muted py-4">
            No content generated yet.
            <a href="{{ route('content.index') }}" class="text-orange-400 hover:underline ml-1">Generate your first post.</a>
        </div>
        @endforelse
    </div>
</div>

{{-- Quick actions --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    @foreach([
        ['label' => 'Generate Content', 'route' => 'content.index',     'desc' => 'AI-powered posts'],
        ['label' => 'View Calendar',    'route' => 'calendar',          'desc' => 'Weekly schedule'],
        ['label' => 'Review Replies',   'route' => 'engagement.index',  'desc' => 'Engagement queue'],
        ['label' => 'Analytics',        'route' => 'kpi',               'desc' => 'KPI breakdown'],
    ] as $action)
    <a href="{{ route($action['route']) }}"
       class="card p-4 hover:opacity-80 transition text-center">
        <div class="text-sm font-semibold mb-0.5" style="color:var(--text-main)">{{ $action['label'] }}</div>
        <div class="text-xs text-muted">{{ $action['desc'] }}</div>
    </a>
    @endforeach
</div>

@endsection