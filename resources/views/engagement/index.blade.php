@extends('layouts.app')
@section('title', 'Engagement Queue')

@section('header-actions')
    <form action="{{ route('engagement.index') }}" method="GET">
        <button type="submit" class="btn-ghost">Refresh Queue</button>
    </form>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Pending Review', 'value' => $stats['pending'],   'color' => '#eab308'],
        ['label' => 'Approved',       'value' => $stats['approved'],  'color' => '#22c55e'],
        ['label' => 'Published',      'value' => $stats['published'], 'color' => '#3b82f6'],
        ['label' => 'Rejected',       'value' => $stats['rejected'],  'color' => '#94a3b8'],
    ] as $stat)
    <div class="card p-5">
        <div class="text-xs text-muted uppercase tracking-wider mb-2">{{ $stat['label'] }}</div>
        <div class="text-3xl font-bold" style="color:{{ $stat['color'] }}">{{ $stat['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- Pending review --}}
@if($pending->isNotEmpty())
<div class="card mb-6">
    <div class="px-5 py-4 border-b border-scrypt-border flex items-center justify-between">
        <div class="text-sm font-semibold" style="color:var(--text-header)">
            Pending Review
            <span class="text-muted font-normal ml-1">({{ $pending->count() }})</span>
        </div>
        <span class="badge badge-contacted">requires approval before posting</span>
    </div>

    @foreach($pending as $item)
    <div class="p-5 border-b border-scrypt-border last:border-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Original post --}}
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-bold uppercase text-orange-400">{{ $item->platform }}</span>
                    <span class="text-xs text-muted">·</span>
                    <span class="text-xs text-muted font-medium">{{ $item->author_name }}</span>
                    <span class="text-xs text-muted">{{ $item->author_handle }}</span>
                    <span class="text-xs text-muted">·</span>
                    <span class="text-xs text-muted">{{ $item->original_posted_at?->diffForHumans() }}</span>
                </div>
                <div class="text-sm leading-relaxed p-3 rounded-lg"
                     style="background:rgba(255,255,255,0.03);border:1px solid var(--border-color);color:var(--text-main)">
                    {{ $item->original_post }}
                </div>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs text-muted">Matched: <span class="text-orange-400">{{ $item->keyword_matched }}</span></span>
                    <span class="text-xs text-muted">Relevance: <span class="font-semibold" style="color:var(--text-main)">{{ $item->relevance_score }}</span></span>
                </div>
            </div>

            {{-- Generated reply + actions --}}
            <div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-2">
                    Generated Reply
                </div>
                <form action="{{ route('engagement.approve', $item) }}" method="POST">
                    @csrf @method('PATCH')
                    <textarea name="reply" rows="4"
                              class="input mb-3 text-sm">{{ $item->generated_reply }}</textarea>
                    <div class="flex gap-2 flex-wrap">
                        <button type="submit" class="btn-primary text-sm">
                            Approve & Publish
                        </button>
                    </div>
                </form>

                <div class="flex gap-2 mt-2">
                    {{-- Regenerate --}}
                    <form action="{{ route('engagement.regenerate', $item) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-ghost text-xs py-1.5 px-3">
                            Regenerate
                        </button>
                    </form>

                    {{-- Reject --}}
                    <form action="{{ route('engagement.reject', $item) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="reason" value="Manually rejected">
                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg transition-all"
                                style="border:1px solid var(--border-color);color:#ef4444"
                                onmouseover="this.style.borderColor='#ef4444'"
                                onmouseout="this.style.borderColor='var(--border-color)'">
                            Reject
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Published --}}
@if($published->isNotEmpty())
<div class="card mb-6">
    <div class="px-5 py-4 border-b border-scrypt-border">
        <div class="text-sm font-semibold" style="color:var(--text-header)">
            Published <span class="text-muted font-normal ml-1">({{ $published->count() }})</span>
        </div>
    </div>
    @foreach($published as $item)
    <div class="flex items-start justify-between px-5 py-4 border-b border-scrypt-border last:border-0">
        <div class="flex-1 min-w-0 pr-4">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase text-orange-400">{{ $item->platform }}</span>
                <span class="text-xs text-muted">{{ $item->author_handle }}</span>
                @if($item->platform_post_url)
                <a href="{{ $item->platform_post_url }}" target="_blank"
                   class="text-xs text-orange-400 hover:underline">View original</a>
                @endif
            </div>
            <div class="text-xs text-muted mb-1">Original: {{ Str::limit($item->original_post, 100) }}</div>
            <div class="text-sm" style="color:var(--text-main)">
                Reply: {{ Str::limit($item->generated_reply, 120) }}
            </div>
        </div>
        <div class="shrink-0 text-right">
            <span class="badge badge-published">published</span>
            <div class="text-xs text-muted mt-1">{{ $item->published_at?->diffForHumans() }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Empty state --}}
@if($pending->isEmpty() && $published->isEmpty())
<div class="card p-12 text-center">
    <div class="text-sm text-muted mb-3">No engagements yet.</div>
    <div class="text-xs text-muted">
        Run the engagement scanner to find and reply to institutional crypto conversations.
    </div>
</div>
@endif

@endsection