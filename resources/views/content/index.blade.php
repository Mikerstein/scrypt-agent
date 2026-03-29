@extends('layouts.app')
@section('title', 'Content Manager')

@section('content')

{{-- Generate form --}}
<div class="card p-6 mb-6">
    <div class="text-sm font-semibold mb-4">Generate New Content</div>
    <form action="{{ route('content.generate') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="text-xs text-muted mb-1.5 block">Content Pillar</label>
                <select name="pillar_id" class="input">
                    @foreach(\App\Models\ContentPillar::where('is_active',true)->get() as $pillar)
                    <option value="{{ $pillar->id }}">{{ $pillar->day_of_week }} — {{ $pillar->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-muted mb-1.5 block">Content Type</label>
                <select name="type" class="input">
                    <option value="linkedin">LinkedIn Post</option>
                    <option value="twitter">X Thread</option>
                    <option value="email">Email Newsletter</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-muted mb-1.5 block">AI Provider</label>
                <select name="provider" class="input">
                    <option value="groq">Groq (Llama 3.3)</option>
                    <option value="anthropic">Anthropic (Claude)</option>
                    <option value="openai">OpenAI (GPT-4o)</option>
                    <option value="gemini">Gemini (Flash 2.0)</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn-primary">Generate Content</button>
    </form>
</div>

{{-- Content list --}}
<div class="card">
    <div class="px-5 py-4 border-b border-scrypt-border flex items-center justify-between">
        <div class="text-sm font-semibold">All Content <span class="text-muted font-normal">({{ $items->total() }})</span></div>
    </div>
    @forelse($items as $item)
    <div class="px-5 py-4 border-b border-scrypt-border last:border-0 hover:bg-white/2 transition">
        <div class="flex items-start justify-between gap-4">
            <a href="{{ route('content.show', $item) }}" class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-400">{{ $item->type }}</span>
                    <span class="text-xs text-slate-600">·</span>
                    <span class="text-xs text-muted">{{ $item->pillar?->name }}</span>
                    <span class="text-xs text-slate-600">·</span>
                    <span class="text-xs text-muted">{{ $item->ai_provider }}</span>
                </div>
                <div class="text-sm text-main leading-relaxed">{{ Str::limit($item->content, 160) }}</div>
            </a>
            <div class="flex items-center gap-3 shrink-0">
                <span class="text-xs text-slate-600">{{ $item->created_at->diffForHumans() }}</span>
                <span class="badge badge-{{ $item->status }}">{{ $item->status }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="px-5 py-12 text-center text-slate-500 text-sm">No content yet. Generate your first post above.</div>
    @endforelse

    @if($items->hasPages())
    <div class="px-5 py-4 border-t border-scrypt-border">{{ $items->links() }}</div>
    @endif
</div>

@endsection