@extends('layouts.app')
@section('title', 'Lead CRM')

@section('content')

{{-- Pipeline stats --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    @foreach($stats as $status => $count)
    <div class="card p-4 text-center">
        <div class="text-2xl font-bold mb-1">{{ $count }}</div>
        <span class="badge badge-{{ $status }}">{{ $status }}</span>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Add lead form --}}
    <div class="card p-5">
        <div class="text-sm font-semibold mb-4">Add Lead</div>
        <form action="{{ route('leads.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs text-muted mb-1 block">Full Name *</label>
                <input type="text" name="name" class="input" required>
            </div>
            <div>
                <label class="text-xs text-muted mb-1 block">Company *</label>
                <input type="text" name="company" class="input" required>
            </div>
            <div>
                <label class="text-xs text-muted mb-1 block">Email</label>
                <input type="email" name="email" class="input">
            </div>
            <div>
                <label class="text-xs text-muted mb-1 block">Title</label>
                <input type="text" name="title" class="input" placeholder="Head of Digital Assets">
            </div>
            <div>
                <label class="text-xs text-muted mb-1 block">Segment *</label>
                <select name="segment" class="input">
                    <option value="hedge_fund">Hedge Fund</option>
                    <option value="bank">Bank</option>
                    <option value="family_office">Family Office</option>
                    <option value="fintech">Fintech</option>
                    <option value="web3">Web3</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-muted mb-1 block">Source *</label>
                <select name="source" class="input">
                    <option value="linkedin">LinkedIn</option>
                    <option value="twitter">X / Twitter</option>
                    <option value="email">Email</option>
                    <option value="referral">Referral</option>
                </select>
            </div>
            <button type="submit" class="btn-primary w-full text-center mt-1">Add Lead</button>
        </form>
    </div>

    {{-- Lead list --}}
    <div class="col-span-2 card">
        <div class="px-5 py-4 border-b border-scrypt-border">
            <div class="text-sm font-semibold">All Leads <span class="text-muted font-normal">({{ $leads->total() }})</span></div>
        </div>
        @forelse($leads as $lead)
        <a href="{{ route('leads.show', $lead) }}" class="flex items-center justify-between px-5 py-4 border-b border-scrypt-border last:border-0 hover:bg-white/2 transition">
            <div>
                <div class="text-sm font-medium">{{ $lead->name }}</div>
                <div class="text-xs text-muted mt-0.5">{{ $lead->company }} · {{ str_replace('_',' ', $lead->segment) }} · via {{ $lead->source }}</div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-600">{{ $lead->activities_count }} activities</span>
                <span class="badge badge-{{ $lead->status }}">{{ $lead->status }}</span>
            </div>
        </a>
        @empty
        <div class="px-5 py-12 text-center text-slate-500 text-sm">No leads yet.</div>
        @endforelse
        @if($leads->hasPages())
        <div class="px-5 py-4 border-t border-scrypt-border">{{ $leads->links() }}</div>
        @endif
    </div>

</div>
@endsection