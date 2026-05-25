{{-- 
    Sidebar Container:
    Di layar kecil (HP), Sidebar mengambil w-full dan h-auto dengan border-b.
    Di layar menengah ke atas (md:), Sidebar dikunci ke w-60, h-screen dengan border-r.
--}}
<aside class="w-full md:w-64 h-auto md:h-screen flex flex-col justify-between p-5 bg-white dark:bg-zinc-900 border-r border-zinc-200/80 dark:border-zinc-800 flex-shrink-0">

    {{-- 
        === 1. BAGIAN ATAS: Logo & Judul Minimalis === 
        Menggunakan Flexbox baris (flex items-center justify-between) untuk menyejajarkan
        logo di sebelah kiri dan indikator status online di sebelah kanan secara horizontal.
    --}}
    <div class="h-16 flex items-center justify-between px-5 flex-shrink-0">
        <div class="flex items-center gap-2.5">
            {{-- Logo gradient indigo khas NexTugas --}}
            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-indigo-600 to-indigo-700 shadow-sm flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <span class="font-extrabold text-sm text-zinc-900 dark:text-zinc-50 tracking-tight">NexTugas</span>
        </div>
        {{-- Indikator status sistem online --}}
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" title="System Online"></span>
    </div>

    {{-- 
        Tombol Utama: Tambah Tugas Baru (+ New Task)
        Tombol ini menggunakan Flexbox (flex items-center justify-center) untuk menyejajarkan 
        icon plus (+) dan teks di tengah secara presisi.
        Desain tombol memiliki border rounded-xl dan shadow indigo modern di mode terang.
    --}}
    <div class="w-full flex justify-center px-2 mb-3 flex-shrink-0">
        <button @click="$dispatch('open-task-modal')"
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl py-2.5 px-4 flex items-center justify-center gap-2 shadow-sm transition-all duration-200 active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            New Task
        </button>
    </div>

    {{-- Search trigger --}}
    <div class="px-4 mb-4 flex-shrink-0">
        <button @click="$dispatch('open-search')" class="w-full relative flex items-center bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-1.5 transition-all hover:border-indigo-500/50 focus:outline-none">
            <svg class="w-3.5 h-3.5 text-zinc-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="bg-transparent text-[11px] text-zinc-400 w-full text-left">Search...</span>
            <span class="text-[9px] font-semibold text-zinc-400 bg-zinc-200/60 dark:bg-zinc-800/80 rounded px-1.5 py-0.5 ml-1 border border-zinc-300/40 dark:border-zinc-700/40 flex-shrink-0">K</span>
        </button>
    </div>

    {{-- 
        === 2. BAGIAN TENGAH: Navigasi Berkelompok ===
        Catatan untuk Guru:
        class flex-1 membuat area navigasi mengisi sisa ruang yang ada.
        class overflow-y-auto memastikan jika menu bertambah panjang,
        maka area menu bisa di-scroll secara mandiri tanpa merusak atau mendorong profil di bawahnya.
    --}}
    <nav class="flex-1 px-3 overflow-y-auto space-y-5">
        
        {{-- Main Menu Group --}}
        <div>
            <p class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest px-3 mb-2">MAIN MENU</p>
            <ul class="space-y-0.5">
                {{-- Dashboard (All Tasks) --}}
                @php
                    $isSemuaActive = request()->routeIs('dashboard') && (!request('filter') || request('filter') === 'semua') && !request('category');
                    $total = auth()->check() ? auth()->user()->tasks()->count() : 0;
                @endphp
                <li>
                    <a href="{{ route('dashboard', ['filter' => 'semua']) }}" 
                       class="flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150
                              {{ $isSemuaActive 
                                 ? 'bg-indigo-50/60 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-semibold' 
                                 : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="truncate whitespace-nowrap">Dashboard</span>
                        </div>
                        @if($total > 0)
                            <span class="text-[10px] rounded-full px-2 py-0.5 font-medium flex-shrink-0
                                        {{ $isSemuaActive 
                                           ? 'bg-indigo-200/50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' 
                                           : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                                {{ $total }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- All Tasks --}}
                @php
                    $isAllActive = request()->routeIs('all');
                @endphp
                <li>
                    <a href="{{ route('all') }}"
                       class="flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150
                              {{ $isAllActive
                                 ? 'bg-indigo-50/60 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-semibold'
                                 : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            <span class="truncate whitespace-nowrap">All</span>
                        </div>
                        @if($total > 0)
                            <span class="text-[10px] rounded-full px-2 py-0.5 font-medium flex-shrink-0
                                        {{ $isAllActive
                                           ? 'bg-indigo-200/50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300'
                                           : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                                {{ $total }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Completed --}}
                @php
                    $isCompletedActive = request()->routeIs('completed');
                    $completed = auth()->check() ? auth()->user()->tasks()->where('status', 'Completed')->count() : 0;
                @endphp
                <li>
                    <a href="{{ route('completed') }}"
                       class="flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150
                              {{ $isCompletedActive
                                 ? 'bg-indigo-50/60 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-semibold'
                                 : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="truncate whitespace-nowrap">Completed</span>
                        </div>
                        @if($completed > 0)
                            <span class="text-[10px] rounded-full px-2 py-0.5 font-medium flex-shrink-0
                                        {{ $isCompletedActive
                                           ? 'bg-indigo-200/50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300'
                                           : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                                {{ $completed }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Urgent --}}
                @php
                    $isPentingActive = request()->routeIs('urgent');
                    $urgent = auth()->check() ? auth()->user()->tasks()->where('status', 'Pending')
                        ->whereNotNull('deadline')
                        ->where('deadline', '<=', now()->addDays(3))
                        ->count() : 0;
                @endphp
                <li>
                    <a href="{{ route('urgent') }}" 
                       class="flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150
                              {{ $isPentingActive 
                                 ? 'bg-indigo-50/60 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-semibold' 
                                 : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="truncate whitespace-nowrap">Urgent</span>
                        </div>
                        @if($urgent > 0)
                            <span class="text-[10px] rounded-full px-2 py-0.5 font-medium flex-shrink-0 animate-pulse
                                        {{ $isPentingActive 
                                           ? 'bg-indigo-200/50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' 
                                           : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                                {{ $urgent }}
                            </span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        {{-- Subjects --}}
        <div>
            <p class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest px-3 mb-2">SUBJECTS</p>
            <ul class="space-y-0.5">
                @php
                    $subjects = [
                        ['name' => 'Mathematics', 'color' => 'bg-blue-500'],
                        ['name' => 'English', 'color' => 'bg-emerald-500'],
                        ['name' => 'Web Programming', 'color' => 'bg-purple-500'],
                        ['name' => 'Database System', 'color' => 'bg-orange-500'],
                        ['name' => 'Others', 'color' => 'bg-zinc-500'],
                    ];
                @endphp
                @foreach($subjects as $sub)
                    @php
                        // Check if we're on the subjects.show route AND the {subject} segment matches this subject
                        $isSubActive = request()->routeIs('subjects.show')
                            && urldecode(request()->route('subject')) === $sub['name'];
                        $subCount = auth()->check() ? auth()->user()->tasks()->where('category', $sub['name'])->count() : 0;
                    @endphp
                    <li>
                        {{-- href links to /subjects/{subject} — the {subject} wildcard resolves to the subject name string --}}
                        <a href="{{ route('subjects.show', ['subject' => $sub['name']]) }}" 
                           class="flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150
                                  {{ $isSubActive 
                                     ? 'bg-indigo-50/60 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-semibold' 
                                     : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-2 h-2 rounded-full {{ $sub['color'] }} flex-shrink-0 shadow-sm"></span>
                                {{-- whitespace-nowrap prevents text wrapping on narrow sidebar widths --}}
                                <span class="truncate whitespace-nowrap">{{ $sub['name'] }}</span>
                            </div>
                            @if($subCount > 0)
                                <span class="text-[9px] font-medium flex-shrink-0
                                            {{ $isSubActive 
                                               ? 'text-indigo-600 dark:text-indigo-400' 
                                               : 'text-zinc-400/80 dark:text-zinc-500' }}">
                                    {{ $subCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    {{-- 
        === 3. BAGIAN BAWAH: Profil Siswa & Theme Switcher Grid ===
        Bagian ini menggunakan Flexbox arah kolom (flex flex-col gap-3)
        untuk menata informasi profil siswa, panel tombol aksi/theme switcher secara vertikal.
    --}}
    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 flex-shrink-0 bg-zinc-50/50 dark:bg-zinc-900/30">
        
        {{-- 
            Dropdown Profil User (Alpine.js)
        --}}
        <div class="relative" x-data="{ userMenuOpen: false }">
            {{-- Kartu Profil: klik untuk memunculkan menu --}}
            <button @click="userMenuOpen = !userMenuOpen" 
                    class="flex items-center gap-3 w-full overflow-hidden p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800/60 text-left transition-colors focus:outline-none border border-transparent focus:border-zinc-200 dark:focus:border-zinc-800">
                {{-- Avatar: Bulletproof conditional photo with storage exists check --}}
                @if(auth()->check() && auth()->user()->profile_photo && \Storage::disk('public')->exists(auth()->user()->profile_photo))
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" class="w-10 h-10 rounded-full object-cover relative z-20 border border-zinc-200 dark:border-zinc-800">
                @else
                    <div class="w-10 h-10 rounded-full bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 font-bold flex items-center justify-center text-sm border border-zinc-200 dark:border-zinc-700 relative z-20">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-zinc-800 dark:text-zinc-100 truncate">{{ auth()->check() ? Auth::user()->name : 'Guest' }}</p>
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 font-medium truncate">XI RPL · NexTugas</p>
                </div>
                <svg class="w-3.5 h-3.5 text-zinc-400 flex-shrink-0 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Floating Menu Pop-up --}}
            <div x-show="userMenuOpen" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
                 @click.outside="userMenuOpen = false" 
                 class="absolute bottom-full left-0 w-full mb-2 bg-white dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800/80 rounded-xl shadow-xl p-1.5 z-50"
                 style="display: none;">
                
                <button @click="userMenuOpen = false; $dispatch('open-settings')" 
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

                <form id="form-logout-sidebar" action="{{ route('logout') }}" method="POST" class="m-0 hidden">
                    @csrf
                </form>
                <button type="button"
                        @click="userMenuOpen = false; $dispatch('open-confirm', {
                            title: 'Logout',
                            message: 'Are you sure you want to log out?',
                            confirmText: 'Logout',
                            formId: 'form-logout-sidebar'
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
</aside>

