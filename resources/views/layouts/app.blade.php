<!DOCTYPE html>
<html lang="id" 
      x-data="{ darkMode: localStorage.getItem('nextugas-theme') === 'dark' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('nextugas-theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
{{--
    Base Layout NexTugas - Laravel 13
    ==============================================
    Alpine.js digunakan untuk mengelola state tema secara global dengan default Light Mode.
    :class="{ 'dark': darkMode }" menambahkan class 'dark' ke tag <html> saat darkMode = true,
    yang kemudian men-trigger semua kelas dark: pada Tailwind CSS.
--}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NexTugas') — AI Task Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN dengan konfigurasi darkMode berbasis class -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>

    <!-- Alpine.js CDN: Library JS ringan untuk interaktivitas (Toggle tema, dropdown, dll) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Transisi halus saat perpindahan tema terang <-> gelap */
        *, *::before, *::after {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.1s ease;
        }
        /* Custom scrollbar untuk sidebar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 2px; }
        .dark ::-webkit-scrollbar-thumb { background: #3f3f46; }
    </style>
</head>

{{--
    Warna background default (Light Mode): bg-zinc-50 text-zinc-900
    Warna saat Dark Mode aktif:            dark:bg-zinc-950 dark:text-zinc-100
    Transisi ini terjadi secara otomatis ketika Alpine.js mengubah state darkMode.

    Catatan untuk Guru:
    class h-screen digunakan untuk mengunci elemen agar setinggi satu layar penuh.
    class overflow-hidden digunakan untuk mencegah scrolling pada seluruh halaman,
    kemudian dipadukan dengan overflow-y-auto di elemen anak agar hanya konten yang panjang yang bisa di-scroll.
--}}
<body class="flex flex-col md:flex-row h-screen w-screen overflow-hidden bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased">

    {{-- Komponen Sidebar --}}
    @include('components.sidebar')

    {{-- Area Konten Utama --}}
    <div class="flex-1 flex flex-col overflow-hidden w-full">

        {{-- Header Atas --}}
        <header class="h-16 border-b border-zinc-200 dark:border-zinc-800/80 bg-white dark:bg-zinc-900/50 backdrop-blur-sm flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-semibold text-zinc-800 dark:text-zinc-100">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-zinc-400">@yield('page-subtitle', 'Welcome back, ' . Auth::user()->name)</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Flash message placeholder removed — Toast handles this globally below --}}

                {{-- Theme Toggle --}}
                <button @click="darkMode = !darkMode"
                        class="p-2 rounded-xl text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 dark:hover:text-zinc-300 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
                        title="Toggle Theme">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                {{-- Dropdown Navbar User --}}
                <div class="relative" x-data="{ navUserMenuOpen: false }">
                    <button @click="navUserMenuOpen = !navUserMenuOpen"
                            class="flex items-center gap-2 focus:outline-none border border-transparent rounded-full focus:border-zinc-200 dark:focus:border-zinc-700">
                        {{-- Avatar: Bulletproof conditional photo with storage exists check --}}
                        @if(auth()->check() && auth()->user()->profile_photo && \Storage::disk('public')->exists(auth()->user()->profile_photo))
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" class="w-10 h-10 rounded-full object-cover relative z-20 border border-zinc-200 dark:border-zinc-800">
                        @else
                            <div class="w-10 h-10 rounded-full bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 font-bold flex items-center justify-center text-sm border border-zinc-200 dark:border-zinc-700 relative z-20">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                            </div>
                        @endif
                    </button>

                    <div x-show="navUserMenuOpen" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 transform scale-95 translate-y-[-10px]"
                         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 transform scale-95 translate-y-[-10px]"
                         @click.outside="navUserMenuOpen = false" 
                         class="absolute right-0 mt-3 w-48 bg-white dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800/80 rounded-xl shadow-xl p-1.5 z-50"
                         style="display: none;">
                        
                        <div class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-800 mb-1">
                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-100 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 font-medium truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <button @click="navUserMenuOpen = false; $dispatch('open-settings')" 
                                class="flex items-center gap-2.5 w-full px-3 py-2 text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-lg transition-colors text-left">
                            <span class="text-sm">⚙️</span>
                            <span>Settings</span>
                        </button>

                        <button @click="darkMode = !darkMode"
                                class="flex items-center gap-2.5 w-full px-3 py-2 text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-lg transition-colors text-left">
                            <svg x-show="darkMode" class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <svg x-show="!darkMode" class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                        </button>

                        <div class="h-px bg-zinc-100 dark:bg-zinc-800 my-1"></div>

                        <form id="form-logout" action="{{ route('logout') }}" method="POST" class="m-0 hidden">
                            @csrf
                        </form>
                        <button type="button" 
                                @click="navUserMenuOpen = false; $dispatch('open-confirm', {
                                    title: 'Logout',
                                    message: 'Are you sure you want to log out?',
                                    confirmText: 'Logout',
                                    formId: 'form-logout'
                                })"
                                class="flex items-center gap-2.5 w-full px-3 py-2 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg transition-colors text-left">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        {{-- Konten Halaman --}}
        <main class="flex-1 overflow-auto w-full">
            @yield('content')
        </main>
    </div>

    <!-- Settings Modal -->
    <!-- 
        Listen for 'open-settings' event on window to show the modal.
    -->
    <div x-data="{ showSettings: false }" 
         @open-settings.window="showSettings = true"
         x-show="showSettings" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4" 
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showSettings" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-zinc-950/40 backdrop-blur-xs"
             @click="showSettings = false"></div>

        <!-- Modal Card -->
        <div x-show="showSettings" 
             x-data="{ activeTab: 'profile' }"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-2xl rounded-2xl shadow-xl z-10 overflow-hidden flex flex-col md:flex-row min-h-[400px]">
             
            <!-- Left Sidebar -->
            <div class="w-full md:w-1/3 bg-zinc-50 dark:bg-zinc-950 border-r border-zinc-200 dark:border-zinc-800 p-5 flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-xl">⚙️</span>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Settings</h3>
                </div>
                <nav class="flex flex-col gap-1">
                    <button @click="activeTab = 'profile'" 
                            :class="activeTab === 'profile' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm border border-zinc-200/50 dark:border-zinc-800/50' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900'"
                            class="px-3 py-2 text-left text-xs font-semibold rounded-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Account Profile
                    </button>
                    <button @click="activeTab = 'appearance'" 
                            :class="activeTab === 'appearance' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm border border-zinc-200/50 dark:border-zinc-800/50' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900'"
                            class="px-3 py-2 text-left text-xs font-semibold rounded-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        Appearance
                    </button>
                </nav>
            </div>

            <!-- Right Main Content -->
            <div class="flex-1 p-6 overflow-y-auto relative bg-white dark:bg-zinc-900">
                <!-- Close Button -->
                <button @click="showSettings = false" class="absolute top-4 right-4 p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors bg-zinc-100 dark:bg-zinc-800 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Profile Tab -->
                <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                     x-data="{ photoPreview: null }">
                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white mb-1">Profile Information</h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mb-6">Update your account profile and photo.</p>
                    
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Profile Photo -->
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" alt="Preview" class="w-16 h-16 rounded-full object-cover border-2 border-zinc-200 dark:border-zinc-700">
                                </template>
                                <template x-if="!photoPreview">
                                    @if(Auth::user()->profile_photo)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile" class="w-16 h-16 rounded-full object-cover border-2 border-zinc-200 dark:border-zinc-700">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-xl font-semibold border-2 border-zinc-200 dark:border-zinc-700">
                                            @php
                                                $nameParts = explode(' ', Auth::user()->name);
                                                $initials = '';
                                                foreach ($nameParts as $part) {
                                                    $initials .= strtoupper(substr($part, 0, 1));
                                                }
                                                $initials = substr($initials, 0, 2);
                                            @endphp
                                            {{ $initials }}
                                        </div>
                                    @endif
                                </template>
                                <label for="profile_photo" class="absolute bottom-0 right-0 w-7 h-7 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-full flex items-center justify-center text-zinc-500 hover:text-indigo-600 dark:hover:text-indigo-400 cursor-pointer shadow-sm transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/jpeg,image/png,image/jpg" @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                                </label>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Profile Photo</span>
                                <span class="text-[10px] text-zinc-500">JPG, PNG max 2MB</span>
                            </div>
                        </div>

                        <div>
                            <label for="display_name" class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">Display Name</label>
                            <input type="text" id="display_name" name="name" value="{{ Auth::user()->name }}" required
                                   class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-xs text-zinc-800 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>

                        <div>
                            <label for="email" class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">Email</label>
                            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required
                                   class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-xs text-zinc-800 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition-all active:scale-[0.98]">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Appearance Tab -->
                <div x-show="activeTab === 'appearance'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white mb-1">App Appearance</h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mb-6">Customize the visual theme of your application.</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <svg x-show="darkMode" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Dark Mode</h5>
                                    <p class="text-[10px] text-zinc-500">Toggle dark appearance.</p>
                                </div>
                            </div>
                            
                            <button @click="darkMode = !darkMode" 
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
                                    :class="darkMode ? 'bg-indigo-500' : 'bg-zinc-300 dark:bg-zinc-700'">
                                <span :class="darkMode ? 'translate-x-5' : 'translate-x-1'"
                                      class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Global Confirm Modal -->
    <div x-data="{
             showConfirm: false,
             title: 'Are you sure?',
             message: 'This action cannot be undone.',
             confirmText: 'Confirm',
             cancelText: 'Cancel',
             type: 'danger',
             formId: null
         }"
         @open-confirm.window="
             title = $event.detail.title || 'Are you sure?';
             message = $event.detail.message || 'This action cannot be undone.';
             confirmText = $event.detail.confirmText || 'Confirm';
             cancelText = $event.detail.cancelText || 'Cancel';
             type = $event.detail.type || 'danger';
             formId = $event.detail.formId || null;
             showConfirm = true;
         "
         x-show="showConfirm"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         style="display: none;">

        <!-- Backdrop -->
        <div x-show="showConfirm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-zinc-950/40 backdrop-blur-xs"
             @click="showConfirm = false"></div>

        <!-- Modal Card -->
        <div x-show="showConfirm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-sm rounded-2xl shadow-xl z-10 p-6 flex flex-col text-center items-center">
             
            <!-- Icon -->
            <div class="w-12 h-12 rounded-full mb-4 flex items-center justify-center"
                 :class="type === 'danger' ? 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2" x-text="title"></h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6" x-text="message"></p>
            
            <div class="flex items-center gap-3 w-full">
                <button @click="showConfirm = false"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-xl transition-colors"
                        x-text="cancelText"></button>
                <button @click="if (formId) { document.getElementById(formId).submit(); } showConfirm = false"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl shadow-sm transition-all active:scale-[0.98]"
                        :class="type === 'danger' ? 'bg-red-600 hover:bg-red-500' : 'bg-amber-600 hover:bg-amber-500'"
                        x-text="confirmText"></button>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- COMMAND PALETTE — Live Search                                      -->
    <!-- Opens: $dispatch('open-search') | Closes: ESC / backdrop click    -->
    <!-- Security: /tasks/search endpoint scoped to auth()->id() only      -->
    <!-- ================================================================= -->
    <div x-data="{
             showSearch: false,
             query: '',
             results: [],
             loading: false,
             timer: null,

             // Fetch tasks from the user-scoped JSON endpoint
             async fetchResults() {
                 if (this.query.length < 2) { this.results = []; return; }
                 this.loading = true;
                 try {
                     const res = await fetch('/tasks/search?q=' + encodeURIComponent(this.query), {
                         headers: { 'X-Requested-With': 'XMLHttpRequest' }
                     });
                     this.results = await res.json();
                 } catch(e) { this.results = []; }
                 this.loading = false;
             },

             // Debounce: wait 300ms after last keystroke before hitting server
             onInput() {
                 clearTimeout(this.timer);
                 this.timer = setTimeout(() => this.fetchResults(), 300);
             },

             open() {
                 this.showSearch = true;
                 this.query = '';
                 this.results = [];
                 this.$nextTick(() => this.$refs.searchInput && this.$refs.searchInput.focus());
             },

             close() {
                 this.showSearch = false;
                 this.query = '';
                 this.results = [];
             }
         }"
         @open-search.window="open()"
         @keydown.escape.window="close()"
         x-show="showSearch"
         class="fixed inset-0 z-50 flex items-start justify-center pt-[10vh] p-4"
         style="display: none;">

        <!-- Backdrop -->
        <div x-show="showSearch"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-zinc-950/40 backdrop-blur-sm"
             @click="close()"></div>

        <!-- Panel -->
        <div x-show="showSearch"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-2xl rounded-2xl shadow-2xl z-10 overflow-hidden flex flex-col">

            <!-- Input Row -->
            <div class="flex items-center px-4 py-4 border-b border-zinc-200 dark:border-zinc-800">
                <template x-if="!loading">
                    <svg class="w-5 h-5 text-zinc-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="w-5 h-5 text-indigo-400 mr-3 flex-shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <input x-ref="searchInput"
                       type="text"
                       x-model="query"
                       @input="onInput()"
                       placeholder="Search your tasks, subjects, or actions..."
                       class="w-full bg-transparent border-none focus:outline-none focus:ring-0 text-base text-zinc-800 dark:text-zinc-100 placeholder-zinc-400">
                <button @click="close()"
                        class="text-[10px] font-semibold text-zinc-500 bg-zinc-100 dark:bg-zinc-800 rounded px-2 py-1 ml-3 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors flex-shrink-0">
                    ESC
                </button>
            </div>

            <!-- Results Body -->
            <div class="overflow-y-auto max-h-[52vh] bg-zinc-50 dark:bg-zinc-950">

                <!-- LIVE RESULTS when query >= 2 chars -->
                <template x-if="query.length >= 2">
                    <div class="p-2">
                        <template x-if="!loading && results.length === 0">
                            <p class="text-xs text-zinc-400 text-center py-10">
                                No tasks found for "<span x-text="query" class="font-semibold text-zinc-600 dark:text-zinc-300"></span>"
                            </p>
                        </template>
                        <template x-if="results.length > 0">
                            <div>
                                <p class="px-3 py-2 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">TASKS</p>
                                <ul class="space-y-1">
                                    <template x-for="task in results" :key="task.id">
                                        <li>
                                            <a :href="'/tasks/' + task.id"
                                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white dark:hover:bg-zinc-900 border border-transparent hover:border-zinc-200 dark:hover:border-zinc-800 transition-all group">
                                                <div class="w-2 h-2 rounded-full flex-shrink-0 shadow-sm"
                                                     :class="{
                                                        'bg-blue-500':    task.category === 'Mathematics',
                                                        'bg-emerald-500': task.category === 'English',
                                                        'bg-purple-500':  task.category === 'Web Programming',
                                                        'bg-orange-500':  task.category === 'Database System',
                                                        'bg-zinc-400':    !task.category || task.category === 'Others'
                                                     }"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" x-text="task.title"></p>
                                                    <p class="text-xs text-zinc-500 truncate">
                                                        <span x-text="task.category || 'No subject'"></span>
                                                        <span class="mx-1">·</span>
                                                        <span x-text="task.status"></span>
                                                    </p>
                                                </div>
                                                <svg class="w-4 h-4 text-zinc-300 dark:text-zinc-600 opacity-0 group-hover:opacity-100 flex-shrink-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- DEFAULT: Navigation suggestions before typing -->
                <template x-if="query.length < 2">
                    <div class="p-2">
                        <p class="px-3 py-2 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">SUGGESTIONS</p>
                        <ul class="space-y-1 mb-3">
                            <li>
                                <a href="{{ route('dashboard', ['filter' => 'semua']) }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-zinc-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-900 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    Go to Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('all') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-zinc-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-900 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    </div>
                                    View All Tasks
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('completed') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-zinc-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-900 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    Completed Tasks
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('urgent') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-zinc-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-900 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    Urgent Tasks
                                </a>
                            </li>
                        </ul>
                        <p class="text-[10px] text-zinc-400 text-center pb-3">Type 2+ characters to search your tasks</p>
                    </div>
                </template>

            </div>
        </div>
    </div>
 

    <!-- New Task Modal -->
    <!--
        Pre-selection: dispatch open-task-modal with { subject: 'Mathematics' }
        Form reset: resetForm() clears all form fields when modal closes.
    -->
    <div x-data="{
             showNewTask: false,
             preSubject: '',
             resetForm() {
                 this.preSubject = '';
                 const form = this.$refs.taskForm;
                 if (form) { form.reset(); }
             }
         }"
         @open-task-modal.window="
             preSubject = ($event.detail && $event.detail.subject) ? $event.detail.subject : '';
             showNewTask = true;
             $nextTick(() => {
                 if (preSubject && $refs.categorySelect) {
                     $refs.categorySelect.value = preSubject;
                 }
             });
         "
         x-show="showNewTask"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">

        <!-- Backdrop -->
        <div x-show="showNewTask"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-zinc-950/40 backdrop-blur-xs"
             @click="showNewTask = false; resetForm()"></div>

        <!-- Modal Card -->
        <div x-show="showNewTask"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-lg rounded-2xl shadow-xl z-10 overflow-hidden flex flex-col p-6">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create New Task
                </h3>
                <button @click="showNewTask = false; resetForm()" class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors bg-zinc-100 dark:bg-zinc-800 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form x-ref="taskForm"
                  x-data="{ isSubmitting: false }"
                  @submit="isSubmitting = true"
                  action="{{ route('tasks.store') }}"
                  method="POST"
                  class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Title</label>
                    <input type="text" name="title" placeholder="What needs to be done?" required
                           class="w-full text-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Description <span class="font-normal text-zinc-400">(Optional)</span></label>
                    <textarea name="description" rows="3" placeholder="Add details..."
                              class="w-full text-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Subject</label>
                        <!-- x-ref="categorySelect" allows JS to pre-select from active filter context -->
                        <select name="category"
                                x-ref="categorySelect"
                                class="w-full text-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none">
                            <option value="">Select Subject...</option>
                            {{-- Subjects unified with sidebar and filter tabs --}}
                            @foreach(['Mathematics', 'English', 'Web Programming', 'Database System', 'Others'] as $sub)
                                <option value="{{ $sub }}">{{ $sub }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Deadline</label>
                        <input type="datetime-local" name="deadline"
                               class="w-full text-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between gap-3">
                    <button type="button"
                            @click="showNewTask = false; resetForm()"
                            class="px-4 py-2.5 text-sm font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" x-bind:disabled="isSubmitting"
                            class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl py-2.5 px-6 flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98] disabled:opacity-75 disabled:cursor-wait">
                        <span x-text="isSubmitting ? 'Creating...' : 'Create Task'">Create Task</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- GLOBAL TOAST NOTIFICATION SYSTEM                                   --}}
    {{-- Triggered by: session('success'), session('error'), Alpine events  --}}
    {{-- Auto-dismisses after 3 seconds. Floats top-right. No alert() used. --}}
    {{-- ================================================================= --}}
    <div
        x-data="toastSystem()"
        x-init="init()"
        @toast.window="addToast($event.detail)"
        class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"
        aria-live="polite">

        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4 scale-95"
                x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                x-transition:leave-end="opacity-0 translate-x-4 scale-95"
                class="pointer-events-auto flex items-center gap-3 min-w-[260px] max-w-xs px-4 py-3 rounded-xl shadow-lg border"
                :class="{
                    'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-100': toast.type === 'success',
                    'bg-white dark:bg-zinc-900 border-red-200 dark:border-red-800 text-zinc-800 dark:text-zinc-100': toast.type === 'error',
                    'bg-white dark:bg-zinc-900 border-amber-200 dark:border-amber-700 text-zinc-800 dark:text-zinc-100': toast.type === 'warning'
                }">

                {{-- Icon --}}
                <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center"
                     :class="{
                         'bg-emerald-50 dark:bg-emerald-500/10': toast.type === 'success',
                         'bg-red-50 dark:bg-red-500/10': toast.type === 'error',
                         'bg-amber-50 dark:bg-amber-500/10': toast.type === 'warning'
                     }">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </template>
                </div>

                {{-- Message --}}
                <p class="flex-1 text-xs font-medium leading-snug" x-text="toast.message"></p>

                {{-- Dismiss button --}}
                <button @click="removeToast(toast.id)"
                        class="flex-shrink-0 w-5 h-5 flex items-center justify-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors rounded">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Progress bar (auto-shrinks over 3s) --}}
                <div class="absolute bottom-0 left-0 h-0.5 rounded-full"
                     :class="{
                         'bg-emerald-400': toast.type === 'success',
                         'bg-red-400': toast.type === 'error',
                         'bg-amber-400': toast.type === 'warning'
                     }"
                     :style="'width:' + toast.progress + '%'"
                     style="transition: width 0.1s linear; position: absolute; bottom: 0; left: 0;"></div>
            </div>
        </template>
    </div>

    {{-- Laravel session flash → Toast bridge --}}
    @if(session('success'))
    <script>
        document.addEventListener('alpine:init', () => {
            // Trigger toast after Alpine is ready
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: @js(session('success')), type: 'success' }
                }));
            }, 200);
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: @js(session('error')), type: 'error' }
                }));
            }, 200);
        });
    </script>
    @endif

    <script>
    function toastSystem() {
        return {
            toasts: [],
            counter: 0,

            init() {
                // Toast system ready — session flash is handled by the blade scripts above
            },

            addToast({ message, type = 'success', duration = 3000 }) {
                const id = ++this.counter;
                this.toasts.push({ id, message, type, visible: true, progress: 100 });

                // Shrink progress bar
                const interval = setInterval(() => {
                    const toast = this.toasts.find(t => t.id === id);
                    if (!toast) { clearInterval(interval); return; }
                    toast.progress = Math.max(0, toast.progress - (100 / (duration / 100)));
                }, 100);

                // Auto-dismiss
                setTimeout(() => this.removeToast(id), duration);
            },

            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) { toast.visible = false; }
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        };
    }
    </script>

</body>
</html>
