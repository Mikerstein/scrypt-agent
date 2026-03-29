@extends('layouts.app')
@section('title', 'Content Detail')

@section('header-actions')
    <a href="{{ route('content.index') }}" class="btn-ghost">← Back</a>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Content --}}
    <div class="lg:col-span-2 card p-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-orange-400">{{ $contentItem->type }}</span>
            <span class="text-slate-600">·</span>
            <span class="text-xs text-muted">{{ $contentItem->pillar?->name }}</span>
            <span class="text-slate-600">·</span>
            <span class="badge badge-{{ $contentItem->status }}">{{ $contentItem->status }}</span>
        </div>
        <div class="text-sm text-main leading-relaxed whitespace-pre-wrap">{{ $contentItem->content }}</div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

        {{-- Status update --}}
        <div class="card p-5">
            <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-3">Update Status</div>
            <form action="{{ route('content.status', $contentItem) }}" method="POST" class="space-y-2">
                @csrf @method('PATCH')
                <select name="status" class="input mb-3">
                    @foreach(['draft','approved','published','rejected'] as $s)
                    <option value="{{ $s }}" {{ $contentItem->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary w-full text-center">Update</button>
            </form>
        </div>

        {{-- Schedule post --}}
        <div class="card p-5">
            <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-3">Schedule Post</div>
            <form action="{{ route('content.schedule', $contentItem) }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="content_item_id" value="{{ $contentItem->id }}">
                <div>
                    <label class="text-xs text-muted mb-1 block">Platform</label>
                    <select name="platform" class="input">
                        <option value="linkedin">LinkedIn</option>
                        <option value="twitter">X / Twitter</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted mb-1 block">Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" class="input"
                           min="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
                <button type="submit" class="btn-primary w-full text-center">Schedule</button>
            </form>
        </div>

        {{-- Meta --}}
        <div class="card p-5 space-y-3">
            <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Details</div>
            @foreach([
                'Provider'  => $contentItem->ai_provider,
                'Model'     => $contentItem->ai_model,
                'Type'      => $contentItem->type,
                'Created'   => $contentItem->created_at->format('M j, Y H:i'),
            ] as $label => $value)
            <div class="flex justify-between text-xs">
                <span class="text-muted">{{ $label }}</span>
                <span class="text-main font-medium">{{ $value }}</span>
            </div>
            @endforeach
        </div>

        {{-- Prompt used --}}
        <div class="card p-5">
            <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-3">Prompt Used</div>
            <div class="text-xs text-muted leading-relaxed">{{ Str::limit($contentItem->prompt_used, 300) }}</div>
        </div>

    </div>
</div>

@endsection