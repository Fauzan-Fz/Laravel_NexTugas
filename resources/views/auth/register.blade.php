<!DOCTYPE html>
<html lang="en"
      x-data="{ darkMode: localStorage.getItem('nextugas-theme') === 'dark' }"
      x-init="$watch('darkMode', val => localStorage.setItem('nextugas-theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexTugas — Sign Up</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .input-glow:focus { box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 w-full h-screen overflow-hidden flex items-center justify-center relative transition-colors duration-300">

    {{-- Background: Dribbble style decorative gradients --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-100/60 dark:bg-indigo-600/10 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-purple-100/40 dark:bg-purple-600/10 rounded-full blur-[80px] translate-y-1/2 -translate-x-1/4"></div>
        {{-- Dot Pattern --}}
        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 28px 28px;"></div>
    </div>

    {{-- Main Register Card: 2 panels (Left Form + Right Illustration) --}}
    <div class="relative z-10 w-full max-w-4xl flex rounded-3xl shadow-2xl shadow-zinc-200/80 dark:shadow-black/40 border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 mx-4 overflow-hidden min-h-[550px]">

        {{-- === LEFT PANEL: Register Form === --}}
        <div class="w-full md:w-[45%] p-8 md:p-10 flex flex-col justify-center bg-white dark:bg-zinc-900/50 dark:backdrop-blur-md">

            {{-- Brand --}}
            <div class="flex items-center gap-2 mb-6">
                <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/25 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="font-bold text-sm text-zinc-800 dark:text-zinc-100 tracking-tight">NexTugas</span>
            </div>

            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50 leading-snug mb-1">
                Create Account
            </h1>
            <p class="text-xs text-zinc-400 mb-6">Start managing your school tasks with AI.</p>

            {{-- Register Form --}}
            <form action="{{ route('register') }}" method="POST" class="space-y-3">
                @csrf

                @if ($errors->any())
                    <div class="p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-500 dark:text-red-400 text-[11px] flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Full Name Input --}}
                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Full Name"
                           class="input-glow w-full pl-9 pr-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:border-indigo-400 dark:focus:border-indigo-500 transition-all">
                </div>

                {{-- Email Input --}}
                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="email@example.com"
                           class="input-glow w-full pl-9 pr-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:border-indigo-400 dark:focus:border-indigo-500 transition-all">
                </div>

                {{-- Password Input --}}
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input :type="show ? 'text' : 'password'" name="password" required
                           placeholder="Password"
                           class="input-glow w-full pl-9 pr-9 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:border-indigo-400 dark:focus:border-indigo-500 transition-all">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
                        <svg x-show="!show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>

                {{-- Confirm Password Input --}}
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                           placeholder="Confirm Password"
                           class="input-glow w-full pl-9 pr-9 py-2 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:border-indigo-400 dark:focus:border-indigo-500 transition-all">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
                        <svg x-show="!show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl py-2.5 flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98] mt-1">
                    Create Account
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <p class="mt-4 text-center text-xs text-zinc-400">
                Already have an account? <a href="{{ route('login') }}" class="text-indigo-500 hover:text-indigo-400 font-medium transition-colors">Sign In</a>
            </p>

            {{-- Dark / Light Mode Toggle (Connected to global state) --}}
            <button @click="darkMode = !darkMode"
                    class="mx-auto mt-5 flex items-center gap-1.5 text-[10px] text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
                <svg x-show="darkMode" class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg x-show="!darkMode" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <span x-text="darkMode ? 'Switch to Light' : 'Switch to Dark'"></span>
            </button>
        </div>

        {{-- === RIGHT PANEL: Background Image === --}}
        <div class="hidden md:flex flex-col w-[55%] relative overflow-hidden">
            {{-- Background Image with dark overlay --}}
            <div class="absolute inset-0">
                <img src="{{ asset('background.jpg') }}" alt="" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-zinc-900/40"></div>
            </div>
        </div>

    </div>
</body>
</html>
