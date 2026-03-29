@extends('layouts.app')
@section('title', 'KPI Analytics')

@section('header-actions')
    <a href="{{ route('dashboard') }}" class="btn-ghost">← Dashboard</a>
@endsection

@section('content')

{{-- Conversion funnel --}}
<div class="card p-6 mb-6">
    <div class="text-sm font-semibold mb-5" style="color:var(--text-header)">Conversion Funnel</div>
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
        @php
        $funnelSteps = [
            ['label' => 'Generated',  'value' => $funnel['content_generated'], 'color' => '#FF6B35'],
            ['label' => 'Published',  'value' => $funnel['content_published'],  'color' => '#3b82f6'],
            ['label' => 'Leads',      'value' => $funnel['leads_total'],         'color' => '#a855f7'],
            ['label' => 'Qualified',  'value' => $funnel['leads_qualified'],     'color' => '#eab308'],
            ['label' => 'Meetings',   'value' => $funnel['meetings'],            'color' => '#22c55e'],
            ['label' => 'Closed',     'value' => $funnel['closed'],              'color' => '#06b6d4'],
        ];
        @endphp
        @foreach($funnelSteps as $i => $step)
        <div class="text-center">
            <div class="text-2xl md:text-3xl font-bold mb-1" style="color:{{ $step['color'] }}">
                {{ $step['value'] }}
            </div>
            <div class="text-xs text-muted">{{ $step['label'] }}</div>
            @if($i < count($funnelSteps) - 1)
            <div class="hidden md:block text-slate-600 text-xs mt-1">↓</div>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- Row 1: Content stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- By type --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Content by Type</div>
        @foreach(['linkedin' => '#0077b5', 'twitter' => '#1da1f2', 'email' => '#FF6B35'] as $type => $color)
        @php $count = $contentByType[$type] ?? 0; $total = $contentByType->sum() ?: 1; @endphp
        <div class="mb-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color:var(--text-main)" class="font-medium capitalize">{{ $type }}</span>
                <span class="text-muted">{{ $count }}</span>
            </div>
            <div class="h-1.5 rounded-full" style="background:var(--border-color)">
                <div class="h-1.5 rounded-full transition-all" style="width:{{ $total ? round(($count/$total)*100) : 0 }}%;background:{{ $color }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- By status --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Content by Status</div>
        @foreach(['draft' => '#94a3b8', 'approved' => '#22c55e', 'published' => '#3b82f6', 'rejected' => '#ef4444'] as $status => $color)
        @php $count = $contentByStatus[$status] ?? 0; $total = $contentByStatus->sum() ?: 1; @endphp
        <div class="mb-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color:var(--text-main)" class="font-medium capitalize">{{ $status }}</span>
                <span class="text-muted">{{ $count }}</span>
            </div>
            <div class="h-1.5 rounded-full" style="background:var(--border-color)">
                <div class="h-1.5 rounded-full transition-all" style="width:{{ round(($count/$total)*100) }}%;background:{{ $color }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- By AI provider --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Content by AI Provider</div>
        @foreach(['groq' => '#FF6B35', 'anthropic' => '#7c3aed', 'openai' => '#22c55e', 'gemini' => '#3b82f6'] as $provider => $color)
        @php $count = $contentByProvider[$provider] ?? 0; $total = $contentByProvider->sum() ?: 1; @endphp
        <div class="mb-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color:var(--text-main)" class="font-medium capitalize">{{ $provider }}</span>
                <span class="text-muted">{{ $count }}</span>
            </div>
            <div class="h-1.5 rounded-full" style="background:var(--border-color)">
                <div class="h-1.5 rounded-full transition-all" style="width:{{ $total ? round(($count/$total)*100) : 0 }}%;background:{{ $color }}"></div>
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- Row 2: Lead stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- By status --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Leads by Status</div>
        @foreach(['new' => '#FF6B35', 'contacted' => '#eab308', 'qualified' => '#a855f7', 'meeting' => '#22c55e', 'closed' => '#3b82f6', 'lost' => '#ef4444'] as $status => $color)
        @php $count = $leadsByStatus[$status] ?? 0; $total = $leadsByStatus->sum() ?: 1; @endphp
        <div class="mb-2.5">
            <div class="flex justify-between text-xs mb-1">
                <span style="color:var(--text-main)" class="font-medium capitalize">{{ $status }}</span>
                <span class="text-muted">{{ $count }}</span>
            </div>
            <div class="h-1.5 rounded-full" style="background:var(--border-color)">
                <div class="h-1.5 rounded-full transition-all" style="width:{{ round(($count/$total)*100) }}%;background:{{ $color }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- By segment --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Leads by Segment</div>
        @foreach(['hedge_fund' => '#FF6B35', 'bank' => '#3b82f6', 'family_office' => '#a855f7', 'fintech' => '#22c55e', 'web3' => '#eab308'] as $segment => $color)
        @php $count = $leadsBySegment[$segment] ?? 0; $total = $leadsBySegment->sum() ?: 1; @endphp
        <div class="mb-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color:var(--text-main)" class="font-medium">{{ str_replace('_',' ', ucfirst($segment)) }}</span>
                <span class="text-muted">{{ $count }}</span>
            </div>
            <div class="h-1.5 rounded-full" style="background:var(--border-color)">
                <div class="h-1.5 rounded-full transition-all" style="width:{{ $total ? round(($count/$total)*100) : 0 }}%;background:{{ $color }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- By source --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Leads by Source</div>
        @foreach(['linkedin' => '#0077b5', 'twitter' => '#1da1f2', 'email' => '#FF6B35', 'referral' => '#22c55e'] as $source => $color)
        @php $count = $leadsBySource[$source] ?? 0; $total = $leadsBySource->sum() ?: 1; @endphp
        <div class="mb-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color:var(--text-main)" class="font-medium capitalize">{{ $source }}</span>
                <span class="text-muted">{{ $count }}</span>
            </div>
            <div class="h-1.5 rounded-full" style="background:var(--border-color)">
                <div class="h-1.5 rounded-full transition-all" style="width:{{ $total ? round(($count/$total)*100) : 0 }}%;background:{{ $color }}"></div>
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- Row 3: Publish stats + AI usage + Weekly content --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- Publish stats --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Publishing Stats</div>
        <div class="space-y-3">
            @foreach([
                ['label' => 'Total Scheduled', 'value' => $publishStats['total_scheduled'], 'color' => '#94a3b8'],
                ['label' => 'Published',        'value' => $publishStats['published'],        'color' => '#3b82f6'],
                ['label' => 'Pending',          'value' => $publishStats['pending'],          'color' => '#eab308'],
                ['label' => 'Failed',           'value' => $publishStats['failed'],           'color' => '#ef4444'],
            ] as $stat)
            <div class="flex items-center justify-between py-2 border-b border-scrypt-border last:border-0">
                <span class="text-xs text-muted">{{ $stat['label'] }}</span>
                <span class="text-sm font-bold" style="color:{{ $stat['color'] }}">{{ $stat['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- AI provider usage --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">AI Provider Usage</div>
        <div class="space-y-3">
            @foreach($aiProviders as $provider)
            <div class="py-2 border-b border-scrypt-border last:border-0">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold capitalize" style="color:var(--text-main)">{{ $provider->name }}</span>
                    <span class="badge {{ $provider->is_active ? 'badge-approved' : 'badge-draft' }}">
                        {{ $provider->is_active ? 'active' : 'inactive' }}
                    </span>
                </div>
                <div class="flex gap-4 text-xs text-muted">
                    <span>{{ $provider->requests_made }} requests</span>
                    <span>{{ $provider->model }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Weekly content volume --}}
    <div class="card p-5">
        <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Weekly Content Volume</div>
        <div class="space-y-3">
            @php $maxWeekly = $weeklyContent->max('count') ?: 1; @endphp
            @foreach($weeklyContent as $week)
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span style="color:var(--text-main)" class="font-medium">{{ $week['week'] }}</span>
                    <span class="text-muted">{{ $week['count'] }} pieces</span>
                </div>
                <div class="h-2 rounded-full" style="background:var(--border-color)">
                    <div class="h-2 rounded-full" style="width:{{ round(($week['count']/$maxWeekly)*100) }}%;background:#FF6B35;transition:width 0.5s"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Content by pillar --}}
<div class="card p-5">
    <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-4">Content by Pillar</div>
    @php $maxPillar = $contentByPillar->max() ?: 1; @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($contentByPillar as $pillar => $count)
        <div>
            <div class="flex justify-between text-xs mb-1">
                <span style="color:var(--text-main)" class="font-medium">{{ $pillar }}</span>
                <span class="text-muted">{{ $count }}</span>
            </div>
            <div class="h-1.5 rounded-full" style="background:var(--border-color)">
                <div class="h-1.5 rounded-full" style="width:{{ round(($count/$maxPillar)*100) }}%;background:#FF6B35"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection