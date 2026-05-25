@extends('layouts.app')

@section('title', 'All Tasks')
@section('page-title', 'All Tasks')
@section('page-subtitle', 'Every task in your workspace — across all statuses')

@section('content')
@php
    $alpineTasks = $tasks->map(fn($t) => ['category' => $t->category ?: 'Others'])->toJson();
@endphp
<div x-data="{ activeSubject: 'All', tasks: {{ $alpineTasks }} }" class="flex-1 h-full overflow-y-auto p-4 md:p-6 w-full flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">All Tasks</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }} total
            </p>
        </div>
        <button @click="$dispatch('open-task-modal')"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            New Task
        </button>
    </div>

    {{-- Dynamic Horizontal Tab Filter (Alpine.js) --}}
    <div class="flex items-center gap-6 border-b border-zinc-200 dark:border-zinc-800/80 overflow-x-auto hide-scrollbar">
        @php
            $tabs = ['All', 'Mathematics', 'English', 'Web Programming', 'Database System', 'Others'];
        @endphp
        @foreach($tabs as $tab)
            <button @click="activeSubject = '{{ $tab }}'"
                    :class="activeSubject === '{{ $tab }}' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'"
                    class="pb-3 text-sm border-b-2 transition-colors duration-200 whitespace-nowrap">
                {{ $tab }}
            </button>
        @endforeach
    </div>

    {{-- Task List --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800/50 rounded-2xl overflow-hidden flex flex-col shadow-sm">
        @forelse($tasks as $task)
            @php
                $isOverdue = $task->deadline && $task->deadline < now() && $task->status !== 'Completed';
                $isUrgent  = !$isOverdue && $task->deadline && $task->deadline <= now()->addDays(3) && $task->status !== 'Completed';
                $isDone    = $task->status === 'Completed';
            @endphp
            {{-- Client-side filter: show if All or matches subject --}}
            <div x-show="activeSubject === 'All' || activeSubject === '{{ $task->category ?: 'Others' }}'"
                 class="relative group flex items-center justify-between p-4 mb-3 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 transition-all hover:border-zinc-300 dark:hover:border-zinc-700">
                <div class="flex items-center space-x-4 min-w-0">
                    <div class="relative z-20 flex items-center justify-center">
                        @if($isDone)
                            <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @elseif($isOverdue)
                            <div class="w-5 h-5 rounded-full border-2 border-red-500 bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            </div>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-zinc-300 dark:border-zinc-600"></div>
                        @endif
                    </div>

                    <div class="min-w-0 flex flex-col">
                        <a href="{{ route('tasks.show', $task->id) }}" class="font-medium text-zinc-900 dark:text-zinc-100 hover:text-indigo-600 transition-colors after:absolute after:inset-0 after:z-10 truncate {{ $isDone ? 'line-through text-zinc-400 dark:text-zinc-500' : '' }}">
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

                    @if(!$isDone)
                        <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline-flex">
                            @csrf @method('PATCH')
                            <button type="submit" title="Mark as Completed" class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                    <form id="form-delete-all-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline-flex">
                        @csrf @method('DELETE')
                        <button type="button" title="Delete Task"
                                @click.prevent="$dispatch('open-confirm', { title: 'Delete Task', message: 'Are you sure you want to delete this task permanently?', confirmText: 'Delete', type: 'danger', formId: 'form-delete-all-{{ $task->id }}' })"
                                class="w-8 h-8 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            {{-- Blade-level empty state (zero tasks in DB) --}}
            <div class="border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl p-8 text-center flex flex-col items-center justify-center min-h-[300px] bg-white dark:bg-zinc-900/50 m-4">
                <div class="w-12 h-12 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4 border border-zinc-100 dark:border-zinc-800">
                    <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">No tasks yet</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">Create your first task to get started.</p>
                <button @click="$dispatch('open-task-modal')"
                        class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm">
                    + New Task
                </button>
            </div>
        @endforelse

        {{-- Alpine.js client-side empty state (filter has no matches) --}}
        @if($tasks->count() > 0)
            <div x-show="tasks.filter(t => activeSubject === 'All' || activeSubject === t.category).length === 0"
                 style="display: none;"
                 class="border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl p-8 text-center flex flex-col items-center justify-center min-h-[300px] bg-white dark:bg-zinc-900/50 m-4">
                <div class="w-12 h-12 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4 border border-zinc-100 dark:border-zinc-800">
                    <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">No results found</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">No tasks match this subject filter.</p>
                <button @click="$dispatch('open-task-modal', { subject: activeSubject })"
                        class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm">
                    + Add Task
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
