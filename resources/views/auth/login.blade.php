<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCRYPT — Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --border: #1a1a1a; }
        body { background: #000000; font-family: 'Inter', sans-serif; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-sm">

    {{-- Logo --}}
    <div class="flex items-center justify-center gap-3 mb-8">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center"
             style="background:#FF6B35">
            <span class="text-white font-bold text-sm">SC</span>
        </div>
        <div>
            <div class="text-white font-bold text-lg leading-none">SCRYPT</div>
            <div class="text-xs mt-0.5" style="color:#475569">Marketing Agent</div>
        </div>
    </div>

    {{-- Card --}}
    <div style="background:#0a0a0a;border:1px solid #1a1a1a;border-radius:16px;padding:32px">

        <h1 class="text-white font-semibold text-lg mb-1">Sign in</h1>
        <p class="text-sm mb-6" style="color:#475569">
            Institutional access only
        </p>

        {{-- Session status --}}
        @if(session('status'))
        <div class="mb-4 text-sm px-4 py-3 rounded-lg"
             style="background:rgba(34,197,94,0.1);color:#22c55e;border:1px solid rgba(34,197,94,0.2)">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color:#94a3b8">
                    Email address
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       style="background:#000;border:1px solid #1a1a1a;color:#e2e8f0;padding:10px 14px;border-radius:8px;font-size:14px;width:100%;outline:none;"
                       onfocus="this.style.borderColor='#FF6B35'"
                       onblur="this.style.borderColor='#1a1a1a'">
                @error('email')
                <p class="text-xs mt-1" style="color:#ef4444">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-medium" style="color:#94a3b8">Password</label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs hover:underline" style="color:#FF6B35">
                        Forgot password?
                    </a>
                    @endif
                </div>
                <input type="password" name="password" required
                       style="background:#000;border:1px solid #1a1a1a;color:#e2e8f0;padding:10px 14px;border-radius:8px;font-size:14px;width:100%;outline:none;"
                       onfocus="this.style.borderColor='#FF6B35'"
                       onblur="this.style.borderColor='#1a1a1a'">
                @error('password')
                <p class="text-xs mt-1" style="color:#ef4444">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember"
                       style="accent-color:#FF6B35;width:14px;height:14px">
                <label for="remember" class="text-xs" style="color:#64748b">
                    Keep me signed in
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    style="background:#FF6B35;color:white;width:100%;padding:10px;border-radius:8px;font-size:14px;font-weight:500;border:none;cursor:pointer;transition:opacity 0.2s;margin-top:4px"
                    onmouseover="this.style.opacity='0.85'"
                    onmouseout="this.style.opacity='1'">
                Sign in to dashboard
            </button>

        </form>
    </div>

    <p class="text-center text-xs mt-6" style="color:#1e293b">
        scrypt.swiss · Zug, Switzerland · FINMA & VQF Licensed
    </p>

</div>

</body>
</html>