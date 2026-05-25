@extends('layouts.app')

@section('title', $task->title . ' — Task Detail')
@section('page-title', 'Task Detail')
@section('page-subtitle', 'View full task info and AI-generated sub-steps')

@section('content')
@php
    /**
     * Compute status labels once for reuse in both panels.
     * isOverdue: deadline passed and task not yet completed.
     */
    $isCompleted = $task->status === 'Completed';
    $isOverdue   = $task->deadline && $task->deadline < now() && !$isCompleted;
    $isUrgent    = !$isOverdue && $task->deadline && $task->deadline <= now()->addDays(3) && !$isCompleted;

    if ($isCompleted)   $statusLabel = 'Completed';
    elseif ($isOverdue) $statusLabel = 'Overdue';
    elseif ($isUrgent)  $statusLabel = 'Urgent';
    else                $statusLabel = 'In Progress';

    // Badge color classes keyed to status label
    $statusBadge = match($statusLabel) {
        'Completed'   => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20',
        'Overdue'     => 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900/50',
        'Urgent'      => 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-500/20',
        default       => 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-100 dark:border-indigo-500/20',
    };

    // Subject dot colors matching sidebar and subject view
    $dotColors = [
        'Mathematics'     => 'bg-blue-500',
        'English'         => 'bg-emerald-500',
        'Web Programming' => 'bg-purple-500',
        'Database System' => 'bg-orange-500',
    ];
    $subjectDot = $dotColors[$task->category] ?? 'bg-zinc-500';
@endphp

<div class="flex-1 h-full overflow-y-auto p-4 md:p-6 w-full flex flex-col gap-6">

    {{-- ============================================================ --}}
    {{-- TOP HEADER: Back button + breadcrumb                         --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        {{-- Clean back button — inline-flex with arrow icon, no default link styling --}}
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
        {{-- Breadcrumb hint --}}
        <p class="text-xs text-zinc-400 dark:text-zinc-500 self-start sm:self-center">
            Task Detail &rsaquo; <span class="text-zinc-600 dark:text-zinc-400 font-medium">{{ Str::limit($task->title, 40) }}</span>
        </p>
    </div>

    {{-- ============================================================ --}}
    {{-- SPLIT PANEL LAYOUT: Left (Task Info) + Right (AI Steps)      --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 flex-1 min-h-0">

        {{-- ========================================================= --}}
        {{-- LEFT PANEL (3/5): Task information                         --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 flex flex-col gap-6 shadow-sm">

            {{-- Panel label --}}
            <div class="flex items-center gap-2 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h2 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Task Info</h2>
            </div>

            {{-- Task title --}}
            <div>
                <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">Title</p>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white leading-snug
                           {{ $isCompleted ? 'line-through text-zinc-400 dark:text-zinc-500' : '' }}">
                    {{ $task->title }}
                </h1>
            </div>

            {{-- Description --}}
            <div>
                <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">Description</p>
                @if($task->description)
                    <p class="text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                        {{ $task->description }}
                    </p>
                @else
                    <p class="text-sm text-zinc-400 dark:text-zinc-600 italic">No description provided.</p>
                @endif
            </div>

            {{-- Meta row: Subject + Status + Deadline --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Subject badge --}}
                <div>
                    <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider mb-2">Subject</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-700 dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 rounded-xl px-3 py-1.5 border border-zinc-200 dark:border-zinc-700">
                        <span class="w-2 h-2 rounded-full {{ $subjectDot }} flex-shrink-0"></span>
                        {{ $task->category ?: 'Others' }}
                    </span>
                </div>

                {{-- Current status badge --}}
                <div>
                    <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider mb-2">Status</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold rounded-xl px-3 py-1.5 border {{ $statusBadge }}">
                        @if($isCompleted)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @elseif($isOverdue)
                            {{-- Overdue logic badge: deadline has passed --}}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @elseif($isUrgent)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                        @endif
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Deadline --}}
                <div>
                    <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider mb-2">Deadline</p>
                    @if($task->deadline)
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-600 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-800 rounded-xl px-3 py-1.5 border border-zinc-200 dark:border-zinc-700
                                     {{ $isOverdue ? 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-900/40' : '' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $task->deadline->format('M d, Y — H:i') }}
                        </span>
                    @else
                        <span class="text-xs text-zinc-400 dark:text-zinc-600 italic">No deadline set</span>
                    @endif
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-3 flex-wrap">
                @if(!$isCompleted)
                    <form action="{{ route('tasks.complete', $task) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Mark as Completed
                        </button>
                    </form>
                @endif
                <form id="form-delete-show-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button"
                            @click.prevent="$dispatch('open-confirm', {
                                title: 'Delete Task',
                                message: 'Are you sure you want to delete this task permanently?',
                                confirmText: 'Delete',
                                type: 'danger',
                                formId: 'form-delete-show-{{ $task->id }}'
                            })"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/30 hover:bg-red-100 dark:hover:bg-red-950/60 border border-red-100 dark:border-red-900/40 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Task
                    </button>
                </form>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- RIGHT PANEL (2/5): AI Assistant Center — Sub-task Steps   --}}
        {{-- Exclusive space for AI-generated checklist from OpenRouter --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 flex flex-col gap-5 shadow-sm">

            {{-- Panel label with AI indicator --}}
            <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    {{-- AI pulse indicator --}}
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-500"></span>
                    </span>
                    <h2 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">AI Assistant Center</h2>
                </div>
                {{-- Step progress chip --}}
                @if($task->steps->count() > 0)
                    @php
                        $completedSteps = $task->steps->where('is_completed', true)->count();
                        $totalSteps     = $task->steps->count();
                    @endphp
                    <span class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 rounded-full px-2.5 py-1 border border-indigo-100 dark:border-indigo-500/20">
                        {{ $completedSteps }}/{{ $totalSteps }} done
                    </span>
                @endif
            </div>

            {{-- Sub-task checklist --}}
            @if($task->steps->count() > 0)
                <p class="text-[11px] text-zinc-400 dark:text-zinc-500">
                    AI-generated sub-steps. Check each off as you go — completing all will mark the task done automatically.
                </p>
                <div class="flex flex-col gap-3">
                    @foreach($task->steps as $step)
                        {{-- Each step is a form-based toggle for reliable server-side state --}}
                        <form action="{{ route('steps.toggle', $step) }}" method="POST" class="flex items-start gap-3">
                            @csrf @method('PATCH')
                            {{-- Interactive checkbox: clicking submits the toggle form --}}
                            <button type="submit"
                                    class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-md border-2 transition-all duration-150 flex items-center justify-center
                                           {{ $step->is_completed
                                               ? 'bg-indigo-600 border-indigo-600 dark:bg-indigo-500 dark:border-indigo-500'
                                               : 'border-zinc-300 dark:border-zinc-600 hover:border-indigo-400 dark:hover:border-indigo-500 bg-white dark:bg-zinc-900' }}"
                                    title="{{ $step->is_completed ? 'Mark incomplete' : 'Mark complete' }}">
                                @if($step->is_completed)
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-zinc-700 dark:text-zinc-200 leading-relaxed
                                           {{ $step->is_completed ? 'line-through text-zinc-400 dark:text-zinc-500' : '' }}">
                                    {{ $step->step_description }}
                                </p>
                            </div>
                        </form>
                    @endforeach
                </div>

                {{-- Progress bar --}}
                @php
                    $pct = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
                @endphp
                <div class="mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">Progress</p>
                        <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400">{{ $pct }}%</p>
                    </div>
                    <div class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full transition-all duration-500"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>

            @elseif($task->steps->isEmpty())
                {{-- Dashed empty state: AI hasn't processed this task yet --}}
                <div class="border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl p-6 text-center flex flex-col items-center justify-center flex-1">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center mb-3 border border-indigo-100 dark:border-indigo-500/20">
                        {{-- Sparkle / AI icon --}}
                        <svg class="w-6 h-6 text-indigo-400 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100 mb-1">AI Processing</h3>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 max-w-[200px] leading-relaxed">
                        Sub-steps are being generated by the AI in the background queue. Refresh in a moment.
                    </p>
                    <button onclick="window.location.reload()"
                            class="mt-3 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
