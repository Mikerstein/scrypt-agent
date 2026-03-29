@extends('layouts.app')
@section('title', 'Lead Detail')

@section('header-actions')
    <a href="{{ route('leads.index') }}" class="btn-ghost">← Back</a>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Lead info + activity --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Header card --}}
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold text-header">{{ $lead->name }}</h2>
                    <div class="text-muted text-sm mt-1">{{ $lead->title }} · {{ $lead->company }}</div>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="badge badge-{{ $lead->status }}">{{ $lead->status }}</span>
                        <span class="text-xs text-muted">{{ str_replace('_',' ', $lead->segment) }}</span>
                        <span class="text-xs text-muted">via {{ $lead->source }}</span>
                    </div>
                </div>
                @if($lead->email)
                <a href="mailto:{{ $lead->email }}" class="btn-primary text-sm">Email Lead</a>
                @endif
            </div>
            @if($lead->notes)
            <div class="mt-4 pt-4 border-t border-scrypt-border text-sm text-muted leading-relaxed">{{ $lead->notes }}</div>
            @endif
        </div>

        {{-- Activity log --}}
        <div class="card p-5">
            <div class="text-sm font-semibold mb-4">Activity Log</div>
            @forelse($lead->activities->sortByDesc('occurred_at') as $activity)
            <div class="flex gap-3 py-3 border-b border-scrypt-border last:border-0">
                <div class="w-2 h-2 rounded-full mt-1.5 shrink-0" style="background:#FF6B35"></div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-orange-400 uppercase">{{ str_replace('_',' ', $activity->type) }}</span>
                        <span class="text-xs text-slate-600">{{ $activity->occurred_at?->diffForHumans() }}</span>
                    </div>
                    <div class="text-sm text-main mt-1">{{ $activity->description }}</div>
                </div>
            </div>
            @empty
            <div class="text-sm text-slate-500">No activities logged yet.</div>
            @endforelse
        </div>

    </div>

    {{-- Sidebar actions --}}
    <div class="space-y-4">

        {{-- Update status --}}
        <div class="card p-5">
            <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-3">Update Status</div>
            <form action="{{ route('leads.status', $lead) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status" class="input">
                    @foreach(['new','contacted','qualified','meeting','closed','lost'] as $s)
                    <option value="{{ $s }}" {{ $lead->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary w-full text-center">Update Status</button>
            </form>
        </div>

        {{-- Log activity --}}
        <div class="card p-5">
            <div class="text-xs font-semibold text-muted uppercase tracking-wider mb-3">Log Activity</div>
            <form action="{{ route('leads.activity', $lead) }}" method="POST" class="space-y-3">
                @csrf
                <select name="type" class="input">
                    <option value="email_sent">Email Sent</option>
                    <option value="call_booked">Call Booked</option>
                    <option value="meeting_held">Meeting Held</option>
                    <option value="follow_up">Follow Up</option>
                    <option value="note">Note</option>
                </select>
                <textarea name="description" rows="3" class="input" placeholder="Add details..." required></textarea>
                <button type="submit" class="btn-primary w-full text-center">Log Activity</button>
            </form>
        </div>

        {{-- Contact details --}}
        <div class="card p-5 space-y-3">
            <div class="text-xs font-semibold text-muted uppercase tracking-wider">Contact Info</div>
            @foreach(['Email' => $lead->email, 'Company' => $lead->company, 'Added' => $lead->created_at->format('M j, Y')] as $label => $value)
            <div class="flex justify-between text-xs">
                <span class="text-muted">{{ $label }}</span>
                <span class="text-main font-medium">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>

    </div>
</div>

@endsection