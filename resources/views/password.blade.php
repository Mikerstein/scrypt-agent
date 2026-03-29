@extends('layouts.app')
@section('title', 'Account Settings')

@section('header-actions')
    <a href="{{ route('dashboard') }}" class="btn-ghost">← Dashboard</a>
@endsection

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    {{-- Profile info --}}
    <div class="card p-6">
        <div class="text-sm font-semibold mb-5" style="color:var(--text-header)">Profile</div>
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold shrink-0"
                 style="background:#FF6B35">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="font-semibold text-base" style="color:var(--text-header)">
                    {{ auth()->user()->name }}
                </div>
                <div class="text-sm text-muted">{{ auth()->user()->email }}</div>
                <div class="text-xs text-muted mt-0.5">
                    Member since {{ auth()->user()->created_at->format('M j, Y') }}
                </div>
            </div>
        </div>

        {{-- Update name & email --}}
        <form method="POST" action="{{ route('account.update-profile') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-medium text-muted mb-1.5 block">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                           class="input" required>
                    @error('name')
                    <p class="text-xs mt-1" style="color:#ef4444">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-muted mb-1.5 block">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                           class="input" required>
                    @error('email')
                    <p class="text-xs mt-1" style="color:#ef4444">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-1">
                <button type="submit" class="btn-primary">Update Profile</button>
            </div>
        </form>
    </div>

    {{-- Change password --}}
    <div class="card p-6">
        <div class="text-sm font-semibold mb-1" style="color:var(--text-header)">Change Password</div>
        <p class="text-xs text-muted mb-5">Use a strong password with at least 8 characters.</p>

        <form method="POST" action="{{ route('account.update-password') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="text-xs font-medium text-muted mb-1.5 block">Current Password</label>
                <input type="password" name="current_password" class="input"
                       placeholder="Enter your current password" required>
                @error('current_password')
                <p class="text-xs mt-1" style="color:#ef4444">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-medium text-muted mb-1.5 block">New Password</label>
                    <input type="password" name="password" class="input"
                           placeholder="Minimum 8 characters" required>
                    @error('password')
                    <p class="text-xs mt-1" style="color:#ef4444">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-muted mb-1.5 block">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="input"
                           placeholder="Repeat new password" required>
                </div>
            </div>

            {{-- Password strength indicator --}}
            <div id="strength-wrap" class="hidden">
                <div class="flex items-center gap-2 mt-1">
                    <div class="flex gap-1 flex-1">
                        <div id="s1" class="h-1 flex-1 rounded-full" style="background:var(--border-color)"></div>
                        <div id="s2" class="h-1 flex-1 rounded-full" style="background:var(--border-color)"></div>
                        <div id="s3" class="h-1 flex-1 rounded-full" style="background:var(--border-color)"></div>
                        <div id="s4" class="h-1 flex-1 rounded-full" style="background:var(--border-color)"></div>
                    </div>
                    <span id="strength-label" class="text-xs text-muted w-16 text-right"></span>
                </div>
            </div>

            <div class="pt-1">
                <button type="submit" class="btn-primary">Update Password</button>
            </div>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="card p-6" style="border-color:rgba(239,68,68,0.2)">
        <div class="text-sm font-semibold mb-1" style="color:#ef4444">Sign Out Everywhere</div>
        <p class="text-xs text-muted mb-4">
            This will invalidate all active sessions across all devices.
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="text-sm px-4 py-2 rounded-lg transition-all font-medium"
                    style="border:1px solid rgba(239,68,68,0.3);color:#ef4444"
                    onmouseover="this.style.background='rgba(239,68,68,0.1)'"
                    onmouseout="this.style.background='transparent'">
                Sign out
            </button>
        </form>
    </div>

</div>

<script>
const pwInput = document.querySelector('input[name="password"]');
const wrap    = document.getElementById('strength-wrap');
const label   = document.getElementById('strength-label');
const bars    = [1,2,3,4].map(i => document.getElementById('s'+i));
const colors  = ['#ef4444','#eab308','#3b82f6','#22c55e'];
const labels  = ['Weak','Fair','Good','Strong'];

pwInput.addEventListener('input', function() {
    const v = this.value;
    if (!v) { wrap.classList.add('hidden'); return; }
    wrap.classList.remove('hidden');

    let score = 0;
    if (v.length >= 8)                    score++;
    if (/[A-Z]/.test(v))                  score++;
    if (/[0-9]/.test(v))                  score++;
    if (/[^A-Za-z0-9]/.test(v))           score++;

    bars.forEach((b, i) => {
        b.style.background = i < score ? colors[score - 1] : 'var(--border-color)';
    });
    label.textContent  = labels[score - 1] || '';
    label.style.color  = colors[score - 1] || 'var(--text-muted)';
});
</script>

@endsection