@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Manage all your tasks, deadlines, and progress')

@section('content')
<div class="flex-1 h-full overflow-y-auto p-4 md:p-6 w-full flex flex-col gap-6">

    {{-- ============================================================ --}}
    {{-- SECTION 1: Welcome Header + Horizontal Statistics Bar        --}}
    {{-- Three stat cards displayed side-by-side in a responsive grid --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col gap-4">
        <div>
            <h1 class="text-xl font-bold text-zinc-900 dark:text-white">
                Welcome back, {{ explode(' ', Auth::user()->name)[0] }} 👋
            </h1>
            </div>

        {{-- Horizontal Stats Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            {{-- Stat: Total Tasks --}}
            <a href="{{ route('dashboard', ['filter' => 'all']) }}"
               class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 flex items-center gap-4 hover:border-indigo-300 dark:hover:border-indigo-500/40 transition-all group">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-500/20 transition-colors">
                    <svg class="w-5 h-5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">Total Tasks</p>
                    <p class="text-2xl font-bold text-zinc-800 dark:text-zinc-50 leading-tight">{{ $stats['total'] }}</p>
                </div>
            </a>

            {{-- Stat: Completed Tasks --}}
            <a href="{{ route('dashboard', ['filter' => 'completed']) }}"
               class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 flex items-center gap-4 hover:border-emerald-300 dark:hover:border-emerald-500/40 transition-all group">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-500/20 transition-colors">
                    <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">Completed</p>
                    <p class="text-2xl font-bold text-zinc-800 dark:text-zinc-50 leading-tight">{{ $stats['completed'] }}</p>
                </div>
            </a>

            {{-- Stat: Urgent Deadlines --}}
            <a href="{{ route('dashboard', ['filter' => 'urgent']) }}"
               class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 flex items-center gap-4 hover:border-amber-300 dark:hover:border-amber-500/40 transition-all group">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-100 dark:group-hover:bg-amber-500/20 transition-colors">
                    <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">Urgent (≤3 Days)</p>
                    <p class="text-2xl font-bold text-zinc-800 dark:text-zinc-50 leading-tight">{{ $stats['deadline'] }}</p>
                </div>
            </a>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- SECTION 2: Main Grid — Task Matrix (Left) + Calendar (Right) --}}
    {{-- ======================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 min-h-0">

        {{-- ========================= --}}
        {{-- LEFT: Active Task Matrix  --}}
        {{-- ========================= --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex flex-col overflow-hidden">

            {{-- Matrix Header --}}
            <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Active Task Board</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-medium text-zinc-400 bg-zinc-100 dark:bg-zinc-800 rounded-full px-2.5 py-1">
                        {{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }}
                    </span>
                    <button @click="$dispatch('open-task-modal')"
                            class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-full px-3 py-1 transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        New
                    </button>
                </div>
            </div>

            {{-- Task List --}}
            <div class="flex-1 overflow-y-auto p-4">
                @forelse($tasks as $task)
                    <div class="relative group flex items-center justify-between p-4 mb-3 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 transition-all hover:border-zinc-300 dark:hover:border-zinc-700">
                        <div class="flex items-center space-x-4 min-w-0">
                            <div class="relative z-20 flex items-center justify-center">
                                @if($task->status === 'Completed')
                                    <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full border-2 border-zinc-300 dark:border-zinc-600"></div>
                                @endif
                            </div>

                            <div class="min-w-0 flex flex-col">
                                <a href="{{ route('tasks.show', $task->id) }}" class="font-medium text-zinc-900 dark:text-zinc-100 hover:text-indigo-600 transition-colors after:absolute after:inset-0 after:z-10 truncate {{ $task->status === 'Completed' ? 'line-through text-zinc-400 dark:text-zinc-500' : '' }}">
                                    {{ $task->title }}
                                </a>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 truncate">
                                    {{ $task->description ?? 'No description' }}
                                </span>
                            </div>
                        </div>

                        <div class="relative z-20 flex items-center space-x-3 shrink-0">
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                {{ $task->category ?: 'Others' }}
                            </span>

                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $task->deadline ? $task->deadline->format('M d, H:i') : 'No deadline' }}
                            </span>

                            @if($task->status !== 'Completed')
                                <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline-flex">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Mark as Completed" class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 flex items-center justify-center transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            <form id="form-delete-dash-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline-flex">
                                @csrf @method('DELETE')
                                <button type="button" title="Delete Task"
                                        @click.prevent="$dispatch('open-confirm', { title: 'Delete Task', message: 'Are you sure you want to delete this task permanently?', confirmText: 'Delete', type: 'danger', formId: 'form-delete-dash-{{ $task->id }}' })"
                                        class="w-8 h-8 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center justify-center transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl p-8 text-center flex flex-col items-center justify-center min-h-[300px] bg-white dark:bg-zinc-900/50 m-4">
                        <div class="w-12 h-12 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4 border border-zinc-100 dark:border-zinc-800">
                            <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">No tasks found</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">Click "New Task" in the sidebar to get started.</p>
                        <button @click="$dispatch('open-task-modal')" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm">
                            + New Task
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- RIGHT: Native Internal Calendar (Self-contained, Alpine.js)    --}}
        {{-- Does NOT connect to Google Calendar or any third-party API.    --}}
        {{-- Only renders current month and marks task deadline dates.      --}}
        {{-- ============================================================= --}}
        <div class="lg:col-span-1 flex flex-col gap-4" 
             x-data="calendarModule()"
             x-init="init()">

            {{-- Calendar Widget --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex-shrink-0">

                {{-- Calendar Header: Month Navigation --}}
                <div class="flex items-center justify-between mb-4">
                    <button @click="prevMonth()"
                            class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <div class="text-center">
                        <p class="text-sm font-bold text-zinc-800 dark:text-zinc-100" x-text="monthName + ' ' + year"></p>
                    </div>
                    <button @click="nextMonth()"
                            class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Day-of-week Labels (Sun–Sat) --}}
                <div class="grid grid-cols-7 mb-1">
                    <template x-for="day in ['S','M','T','W','T','F','S']">
                        <div class="text-center text-[10px] font-bold text-zinc-400 uppercase py-1" x-text="day"></div>
                    </template>
                </div>

                {{-- Calendar Grid: CSS Grid 7 columns --}}
                <div class="grid grid-cols-7 gap-0.5">
                    {{-- 
                        Leading empty cells: fill the days before the 1st of the month.
                        startDayOffset = weekday index (0=Sun) of the 1st day.
                    --}}
                    <template x-for="i in startDayOffset" :key="'empty-' + i">
                        <div class="h-8"></div>
                    </template>

                    {{-- Day Cells: iterate through all days in the current month --}}
                    <template x-for="day in daysInMonth" :key="day">
                        <div class="relative h-8 flex items-center justify-center">
                            {{-- Deadline indicator dot (shown if this date has a task deadline) --}}
                            <template x-if="hasDeadline(day)">
                                <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-amber-500"></span>
                            </template>

                            <button
                                @click="selectedDay = day"
                                class="w-7 h-7 text-xs rounded-full flex items-center justify-center transition-colors font-medium"
                                :class="{
                                    'bg-indigo-600 text-white font-bold shadow-sm': isToday(day),
                                    'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400': selectedDay === day && !isToday(day),
                                    'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800': selectedDay !== day && !isToday(day)
                                }"
                                x-text="day">
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Upcoming Deadlines Mini-List --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex-1">
                <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3">Upcoming Deadlines</h4>
                <div class="space-y-3">
                    @php
                        $upcoming = $tasks
                            ->where('status', '!=', 'Completed')
                            ->whereNotNull('deadline')
                            ->sortBy('deadline')
                            ->take(5);
                    @endphp

                    @forelse($upcoming as $upTask)
                        @php
                            $daysLeft = now()->startOfDay()->diffInDays($upTask->deadline->startOfDay(), false);
                        @endphp
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0
                                        {{ $daysLeft < 0 ? 'bg-red-500' : ($daysLeft <= 3 ? 'bg-amber-500' : 'bg-indigo-400') }}">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $upTask->title }}</p>
                                <p class="text-[10px] text-zinc-400 mt-0.5">
                                    @if($daysLeft < 0)
                                        <span class="text-red-500 font-semibold">{{ abs($daysLeft) }}d overdue</span>
                                    @elseif($daysLeft === 0)
                                        <span class="text-amber-500 font-semibold">Due today</span>
                                    @elseif($daysLeft === 1)
                                        <span class="text-amber-500 font-semibold">Tomorrow</span>
                                    @else
                                        <span class="{{ $daysLeft <= 3 ? 'text-amber-500' : 'text-zinc-400' }}">
                                            In {{ $daysLeft }} days
                                        </span>
                                    @endif
                                    · {{ $upTask->deadline->format('d M') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-400 italic">No upcoming deadlines. 🎉</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- Alpine.js Module: Native Internal Calendar Logic               --}}
{{-- Reads deadline dates from PHP-injected JSON array (no API).    --}}
{{-- ============================================================== --}}
<script>
function calendarModule() {
    return {
        // Current display state
        year: 0,
        month: 0,       // 0-indexed (0 = January)
        selectedDay: null,

        // Deadline dates from internal task data (array of day numbers for current month)
        // Populated by init() by cross-referencing PHP task deadlines with the current view
        deadlineDays: [],

        // PHP-injected task deadlines as ISO date strings (passed from Blade)
        taskDeadlines: {!! $tasks->whereNotNull('deadline')->map(fn($t) => $t->deadline->toDateString())->values()->toJson() !!},

        init() {
            const now = new Date();
            this.year  = now.getFullYear();
            this.month = now.getMonth();
            this.selectedDay = now.getDate();
            this.computeDeadlines();
        },

        // Recompute which day numbers in the current month have a deadline
        computeDeadlines() {
            const padded = String(this.month + 1).padStart(2, '0');
            const prefix = `${this.year}-${padded}-`;
            this.deadlineDays = this.taskDeadlines
                .filter(d => d.startsWith(prefix))
                .map(d => parseInt(d.split('-')[2], 10));
        },

        // Navigate to previous month
        prevMonth() {
            if (this.month === 0) { this.month = 11; this.year--; }
            else { this.month--; }
            this.selectedDay = null;
            this.computeDeadlines();
        },

        // Navigate to next month
        nextMonth() {
            if (this.month === 11) { this.month = 0; this.year++; }
            else { this.month++; }
            this.selectedDay = null;
            this.computeDeadlines();
        },

        // Returns true if the given day number has at least one task deadline
        hasDeadline(day) {
            return this.deadlineDays.includes(day);
        },

        // Returns true if the given day is today
        isToday(day) {
            const now = new Date();
            return day === now.getDate()
                && this.month === now.getMonth()
                && this.year  === now.getFullYear();
        },

        // Total number of days in the current displayed month
        get daysInMonth() {
            return new Date(this.year, this.month + 1, 0).getDate();
        },

        // How many empty cells to render before the 1st of the month (0=Sun offset)
        get startDayOffset() {
            return new Date(this.year, this.month, 1).getDay();
        },

        // Full month name string (e.g., "June")
        get monthName() {
            return new Date(this.year, this.month, 1).toLocaleString('en-US', { month: 'long' });
        }
    };
}
</script>
@endsection
