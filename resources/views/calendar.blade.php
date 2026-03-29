@extends('layouts.app')
@section('title', 'Content Calendar')

@section('header-actions')
    <a href="{{ route('content.index') }}" class="btn-primary">+ Generate Content</a>
@endsection

@section('content')

{{-- Week grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3 mb-8">
    @foreach($calendar as $slot)
    <div class="card p-4 {{ $slot['today'] ? 'ring-1 ring-orange-500' : '' }}">

        {{-- Day header --}}
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider {{ $slot['today'] ? 'text-orange-400' : 'text-slate-500' }}">
                {{ substr($slot['day'], 0, 3) }}
            </span>
            @if($slot['today'])
            <span class="text-xs px-1.5 py-0.5 rounded font-semibold" style="background:rgba(255,107,53,0.15);color:#FF6B35">Today</span>
            @endif
        </div>

        {{-- Pillar --}}
        @if($slot['pillar'])
        <div class="text-xs font-semibold text-white mb-3 leading-snug">{{ $slot['pillar']->name }}</div>

        {{-- Content items --}}
        <div class="space-y-1.5">
            @forelse($slot['content'] as $item)
            <a href="{{ route('content.show', $item) }}"
               class="block text-xs px-2 py-1.5 rounded-md hover:opacity-80 transition truncate"
               style="background:rgba(255,255,255,0.04)">
                <span class="font-semibold text-orange-400 uppercase mr-1">{{ substr($item->type,0,2) }}</span>
                <span class="badge badge-{{ $item->status }} ml-1">{{ $item->status }}</span>
            </a>
            @empty
            <div class="text-xs text-slate-600 italic">No content yet</div>
            @endforelse
        </div>
        @else
        <div class="text-xs text-slate-600 italic">No pillar</div>
        @endif

    </div>
    @endforeach
</div>

{{-- Upcoming scheduled posts --}}
<div class="card">
    <div class="px-5 py-4 border-b border-scrypt-border">
        <div class="text-sm font-semibold text-white">
            Upcoming Scheduled Posts
            <span class="text-slate-500 font-normal">({{ $upcoming->count() }})</span>
        </div>
    </div>

    @forelse($upcoming as $post)
    <div class="flex items-center justify-between px-5 py-4 border-b border-scrypt-border last:border-0">
        <div class="flex-1 min-w-0 pr-4">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase text-orange-400">{{ $post->platform }}</span>
                <span class="text-xs text-slate-600">·</span>
                <span class="text-xs text-slate-500">
                    {{ Str::limit($post->contentItem->content, 80) }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-xs text-slate-400 font-medium">
                {{ Carbon\Carbon::parse($post->scheduled_at)->format('M j · H:i') }}
            </span>
            <span class="badge badge-new">pending</span>
        </div>
    </div>
    @empty
    <div class="px-5 py-10 text-center text-slate-500 text-sm">
        No scheduled posts yet.
        <a href="{{ route('content.index') }}" class="text-orange-400 hover:underline ml-1">Generate and schedule content.</a>
    </div>
    @endforelse
</div>

@endsection