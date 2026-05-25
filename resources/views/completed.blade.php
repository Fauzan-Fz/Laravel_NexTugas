@extends('layouts.app')

@section('title', 'Completed Tasks')
@section('page-title', 'Completed Tasks')
@section('page-subtitle', 'All finished tasks — well done!')

@section('content')
@php
    $alpineTasks = $tasks->map(fn($t) => ['category' => $t->category ?: 'Others'])->toJson();
@endphp
<!-- Initialize Alpine.js data for client-side zero-reload filtering -->
<div x-data="{ activeSubject: 'All', tasks: {{ $alpineTasks }} }" class="flex-1 h-full overflow-y-auto p-4 md:p-6 w-full flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Completed Tasks</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }} marked as done
            </p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    {{-- Dynamic Horizontal Tab Filter (Alpine.js) --}}
    <!-- The activeSubject determines which tasks are shown instantly -->
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
            {{-- Client-side filtering check: display if 'All' or matches task's category --}}
            <div x-show="activeSubject === 'All' || activeSubject === '{{ $task->category ?: 'Others' }}'"
                 class="relative group flex items-center justify-between p-4 mb-3 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 transition-all hover:border-zinc-300 dark:hover:border-zinc-700">
                <div class="flex items-center space-x-4 min-w-0">
                    <div class="relative z-20 flex items-center justify-center">
                        <div class="w-5 h-5 rounded-full bg-emerald-500 dark:bg-emerald-500/20 border border-emerald-500 dark:border-emerald-500/30 flex items-center justify-center opacity-70">
                            <svg class="w-3 h-3 text-white dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>

                    <div class="min-w-0 flex flex-col">
                        <a href="{{ route('tasks.show', $task->id) }}" class="font-medium line-through text-zinc-500 dark:text-zinc-400 hover:text-indigo-500 transition-colors after:absolute after:inset-0 after:z-10 truncate">
                            {{ $task->title }}
                        </a>
                        <span class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 truncate">
                            {{ $task->description ?? 'No description' }}
                        </span>
                    </div>
                </div>

                <div class="relative z-20 flex items-center space-x-3 shrink-0">
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 opacity-80">
                        {{ $task->category ?: 'Others' }}
                    </span>

                    <span class="text-xs text-zinc-400 dark:text-zinc-500">
                        {{ $task->deadline ? $task->updated_at->format('M d, Y') : 'No date' }}
                    </span>

                    <form id="form-delete-completed-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline-flex">
                        @csrf @method('DELETE')
                        <button type="button" title="Remove from history"
                                @click.prevent="$dispatch('open-confirm', { title: 'Remove Task', message: 'Are you sure you want to remove this completed task permanently?', confirmText: 'Remove', type: 'danger', formId: 'form-delete-completed-{{ $task->id }}' })"
                                class="w-8 h-8 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 flex items-center justify-center transition-all">
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
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">You haven't completed any tasks yet.</p>
                <button @click="$dispatch('open-task-modal')" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm">
                    + Add Task
                </button>
            </div>
        @endforelse

        {{-- Client-side filtering empty state check --}}
        @if($tasks->count() > 0)
            <!-- Calculates if any task matches the activeSubject filter by checking array length in Alpine.js -->
            <div x-show="tasks.filter(t => activeSubject === 'All' || activeSubject === t.category).length === 0" 
                 style="display: none;"
                 class="border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl p-8 text-center flex flex-col items-center justify-center min-h-[300px] bg-white dark:bg-zinc-900/50 m-4">
                <div class="w-12 h-12 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4 border border-zinc-100 dark:border-zinc-800">
                    <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">No results found</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">You haven't completed any tasks for this subject yet.</p>
                <button @click="$dispatch('open-task-modal', { subject: activeSubject !== 'All' ? activeSubject : '' })" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors shadow-sm">
                    + Add Task
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
