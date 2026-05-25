@extends('layouts.app')

@section('title', $subjectName . ' — Tasks')
@section('page-title', $subjectName)
@section('page-subtitle', 'All tasks for this subject, filtered by status')

@section('content')
@php
    /**
     * Build the Alpine.js task array with status metadata for client-side filtering.
     * Each row stores: category, rawStatus (DB value), and computed taskStatus label
     * matching one of the 5 filter tabs: All | In Progress | Completed | Urgent | Overdue.
     */
    $alpineTasks = $tasks->map(function ($t) {
        $isOverdue   = $t->deadline && $t->deadline < now() && $t->status !== 'Completed';
        $isUrgent    = !$isOverdue && $t->deadline && $t->deadline <= now()->addDays(3) && $t->status !== 'Completed';
        $isCompleted = $t->status === 'Completed';

        if ($isOverdue)        $tabLabel = 'Overdue';
        elseif ($isCompleted)  $tabLabel = 'Completed';
        elseif ($isUrgent)     $tabLabel = 'Urgent';
        else                   $tabLabel = 'In Progress';

        return [
            'id'        => $t->id,
            'category'  => $t->category ?: 'Others',
            'taskStatus'=> $tabLabel,
        ];
    })->toJson();
@endphp

{{-- Alpine component: activeStatus drives zero-reload client-side filter across all rows --}}
<div x-data="{ activeStatus: 'All', tasks: {{ $alpineTasks }} }"
     class="flex-1 h-full overflow-y-auto p-4 md:p-6 w-full flex flex-col gap-6">

    {{-- ============================================================ --}}
    {{-- HEADER: Subject title + Back button                          --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                {{-- Color dot to visually identify the subject --}}
                @php
                    $dotColors = [
                        'Mathematics'    => 'bg-blue-500',
                        'English'        => 'bg-emerald-500',
                        'Web Programming'=> 'bg-purple-500',
                        'Database System'=> 'bg-orange-500',
                    ];
                    $dot = $dotColors[$subjectName] ?? 'bg-zinc-500';
                @endphp
                <span class="w-3 h-3 rounded-full {{ $dot }} flex-shrink-0"></span>
                {{ $subjectName }}
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }} in this subject
            </p>
        </div>
        {{-- Clean back button — icon + label, no default blue link styling --}}
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    {{-- ============================================================ --}}
    {{-- STATUS FILTER TABS — Alpine.js driven, zero page reload      --}}
    {{-- Tabs: All | In Progress | Completed | Urgent | Overdue       --}}
    {{-- activeStatus controls which rows are shown via x-show below  --}}
    {{-- ============================================================ --}}
    <div class="flex items-center gap-6 border-b border-zinc-200 dark:border-zinc-800/80 overflow-x-auto hide-scrollbar">
        @foreach(['All', 'In Progress', 'Completed', 'Urgent', 'Overdue'] as $tab)
            <button @click="activeStatus = '{{ $tab }}'"
                    :class="activeStatus === '{{ $tab }}'
                        ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold'
                        : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'"
                    class="pb-3 text-sm border-b-2 transition-colors duration-200 whitespace-nowrap">
                {{ $tab }}
                {{-- Live count badge per tab using Alpine computed filter --}}
                <span class="ml-1 text-xs"
                      x-text="'(' + tasks.filter(t => '{{ $tab }}' === 'All' || t.taskStatus === '{{ $tab }}').length + ')'">
                </span>
            </button>
        @endforeach
    </div>

    {{-- ============================================================ --}}
    {{-- TASK DATA ROWS — Horizontal row style matching completed page --}}
    {{-- x-show applies Alpine filter: shows row if All OR status match --}}
    {{-- ============================================================ --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800/50 rounded-2xl overflow-hidden flex flex-col shadow-sm">
        @forelse($tasks as $task)
            @php
                $isOverdue   = $task->deadline && $task->deadline < now() && $task->status !== 'Completed';
                $isUrgent    = !$isOverdue && $task->deadline && $task->deadline <= now()->addDays(3) && $task->status !== 'Completed';
                $isCompleted = $task->status === 'Completed';

                if ($isOverdue)        $tabLabel = 'Overdue';
                elseif ($isCompleted)  $tabLabel = 'Completed';
                elseif ($isUrgent)     $tabLabel = 'Urgent';
                else                   $tabLabel = 'In Progress';
            @endphp

            {{-- x-show: display if activeStatus is 'All' OR equals this task's computed status label --}}
            <div x-show="activeStatus === 'All' || activeStatus === '{{ $tabLabel }}'"
                 class="relative group flex items-center justify-between p-4 mb-3 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 transition-all hover:border-zinc-300 dark:hover:border-zinc-700">
                <div class="flex items-center space-x-4 min-w-0">
                    <div class="relative z-20 flex items-center justify-center">
                        @if($isCompleted)
                            <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @elseif($isOverdue)
                            <div class="w-5 h-5 rounded-full border-2 border-red-500 bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            </div>
                        @elseif($isUrgent)
                            <div class="w-5 h-5 rounded-full border-2 border-amber-400 dark:border-amber-500"></div>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-zinc-300 dark:border-zinc-600"></div>
                        @endif
                    </div>

                    <div class="min-w-0 flex flex-col">
                        <a href="{{ route('tasks.show', $task->id) }}" class="font-medium hover:text-indigo-600 transition-colors after:absolute after:inset-0 after:z-10 truncate {{ $isCompleted ? 'line-through text-zinc-400 dark:text-zinc-500' : 'text-zinc-900 dark:text-white' }}">
                            {{ $task->title }}
                        </a>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 truncate">
                            {{ $task->description ?? 'No description' }}
                        </span>
                    </div>
                </div>

                <div class="relative z-20 flex items-center space-x-3 shrink-0">
                    @if($isCompleted)
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Completed</span>
                    @elseif($isOverdue)
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">Overdue</span>
                    @elseif($isUrgent)
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">Urgent</span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">In Progress</span>
                    @endif

                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $task->deadline ? $task->deadline->format('M d, Y') : 'No deadline' }}
                    </span>

                    <a href="{{ route('tasks.show', $task->id) }}" title="View Detail" class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    @if(!$isCompleted)
                        <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline-flex">
                            @csrf @method('PATCH')
                            <button type="submit" title="Mark as Done" class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                    <form id="form-delete-subject-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline-flex">
                        @csrf @method('DELETE')
                        <button type="button" title="Delete Task"
                                @click.prevent="$dispatch('open-confirm', { title: 'Delete Task', message: 'Are you sure you want to delete this task permanently?', confirmText: 'Delete', type: 'danger', formId: 'form-delete-subject-{{ $task->id }}' })"
                                class="w-8 h-8 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            {{-- Blade-level empty state: subject has zero tasks in DB --}}
            <div class="border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl p-8 text-center flex flex-col items-center justify-center min-h-[300px] bg-white dark:bg-zinc-900/50 m-4">
                <div class="w-12 h-12 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4 border border-zinc-100 dark:border-zinc-800">
                    <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">No tasks for {{ $subjectName }}</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">Create your first task for this subject to get started.</p>
                <button @click="$dispatch('open-task-modal', { subject: '{{ $subjectName }}' })"
                        class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm">
                    + Add Task
                </button>
            </div>
        @endforelse

        {{-- Alpine-level empty state: tasks exist but none match the active status tab --}}
        @if($tasks->count() > 0)
            {{-- x-show calculates whether filtered result set is empty for the current tab --}}
            <div x-show="tasks.filter(t => activeStatus === 'All' || activeStatus === t.taskStatus).length === 0"
                 style="display: none;"
                 class="border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl p-8 text-center flex flex-col items-center justify-center min-h-[200px] bg-white dark:bg-zinc-900/50 m-4">
                <div class="w-12 h-12 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4 border border-zinc-100 dark:border-zinc-800">
                    <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">No results for this status</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">
                    No <span x-text="activeStatus"></span> tasks found for {{ $subjectName }}.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
