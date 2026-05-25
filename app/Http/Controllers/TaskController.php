<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStep;
use App\Jobs\GenerateTaskStepsJob;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

// PHP Attribute Middleware — ensures 'web' middleware (Session, CSRF, Cookie) is active
#[\Illuminate\Routing\Controllers\Middleware('web')]
class TaskController extends Controller implements HasMiddleware
{
    /**
     * Declare middleware explicitly.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('web'),
        ];
    }

    // ─── PRIVATE HELPER ────────────────────────────────────────────────────────
    /**
     * Returns a base query builder scoped to the authenticated user's tasks only.
     * Data privacy: NEVER query Task:: without this scope to prevent cross-user leaks.
     */
    private function userTasks(): \Illuminate\Database\Eloquent\Builder
    {
        return Task::where('user_id', auth()->id());
    }

    // ─── DASHBOARD ─────────────────────────────────────────────────────────────
    /**
     * Display the dashboard with the current user's tasks.
     * Supports JSON responses for API clients.
     */
    public function index(Request $request)
    {
        // Base query — scoped to current user only
        $query = $this->userTasks()->with('steps');

        // Dynamic filter: completed or urgent
        $filter = $request->get('filter');
        if ($filter === 'completed') {
            $query->where('status', 'Completed');
        } elseif ($filter === 'urgent') {
            $query->where('status', 'Pending')
                  ->whereNotNull('deadline')
                  ->where('deadline', '<=', now()->addDays(3));
        }

        // Optional subject/category filter
        $category = $request->get('category');
        if ($category) {
            $query->where('category', $category);
        }

        $tasks = $query->latest()->get();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $tasks]);
        }

        // Dashboard stats — scoped to current user
        $stats = [
            'total'     => $this->userTasks()->count(),
            'completed' => $this->userTasks()->where('status', 'Completed')->count(),
            'deadline'  => $this->userTasks()->where('deadline', '<=', now()->addDays(3))
                               ->where('status', 'Pending')->count(),
        ];

        return view('dashboard', compact('tasks', 'stats'));
    }

    // ─── CREATE ────────────────────────────────────────────────────────────────
    /**
     * Store a new task, associate it with the authenticated user,
     * then dispatch the AI step-generation job asynchronously via Queue.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'deadline'    => 'nullable|date',
        ]);

        // Assign the task to the authenticated user — enforces data ownership
        $task = Task::create([
            'user_id'     => auth()->id(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category'    => $validated['category'] ?? null,
            'deadline'    => $validated['deadline'] ?? null,
            'status'      => 'Pending',
        ]);

        // Dispatch AI sub-task generation to the background queue (non-blocking)
        GenerateTaskStepsJob::dispatch($task);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created! AI is processing sub-steps in the background.',
                'data'    => $task,
            ], 201);
        }

        return redirect()->route('dashboard')->with('success', 'Task created! AI is processing sub-steps in the background.');
    }

    // ─── DELETE ────────────────────────────────────────────────────────────────
    /**
     * Delete a task. Route model binding only resolves tasks belonging to the user
     * because the query is scoped; this provides implicit authorization.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('dashboard')->with('success', 'Task deleted successfully.');
    }

    // ─── COMPLETE ──────────────────────────────────────────────────────────────
    /**
     * Mark a task as Completed.
     */
    public function complete(Task $task)
    {
        $task->update(['status' => 'Completed']);
        return redirect()->route('dashboard')->with('success', 'Task marked as completed!');
    }

    // ─── TOGGLE STEP ───────────────────────────────────────────────────────────
    /**
     * Toggle the completion status of an individual AI-generated step.
     * Automatically marks the parent task as Completed if all steps are done.
     */
    public function toggleStep(TaskStep $step)
    {
        $step->update(['is_completed' => !$step->is_completed]);

        $task         = $step->task;
        $totalSteps   = $task->steps()->count();
        $pendingSteps = $task->steps()->where('is_completed', false)->count();

        if ($pendingSteps === 0 && $totalSteps > 0) {
            $task->update(['status' => 'Completed']);
        } elseif ($task->status === 'Completed') {
            $task->update(['status' => 'Pending']);
        }

        return redirect()->back()->with('success', 'Step status updated!');
    }

    // ─── COMPLETED VIEW ────────────────────────────────────────────────────────
    /**
     * Show all completed tasks for the current user.
     */
    public function completed()
    {
        $tasks = $this->userTasks()->with('steps')
            ->where('status', 'Completed')
            ->latest()
            ->get();

        return view('completed', compact('tasks'));
    }

    // ─── ALL VIEW ──────────────────────────────────────────────────────────────
    /**
     * Show all tasks (any status) for the current user.
     */
    public function all()
    {
        $tasks = $this->userTasks()->with('steps')
            ->latest()
            ->get();

        return view('all', compact('tasks'));
    }

    // ─── URGENT VIEW ───────────────────────────────────────────────────────────
    /**
     * Show urgent tasks (Pending with deadline within 3 days) for the current user.
     */
    public function urgent()
    {
        $tasks = $this->userTasks()->with('steps')
            ->where('status', 'Pending')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(3))
            ->orderBy('deadline', 'asc')
            ->get();

        return view('urgent', compact('tasks'));
    }

    // ─── SUBJECT VIEW ──────────────────────────────────────────────────────────
    /**
     * Dynamic subject/category view — scoped to the current user's tasks.
     */
    public function subject(string $subjectName)
    {
        $subjectName = urldecode($subjectName);

        $tasks = $this->userTasks()->with('steps')
            ->where('category', $subjectName)
            ->latest()
            ->get();

        return view('subject', compact('tasks', 'subjectName'));
    }

    // ─── TASK DETAIL ───────────────────────────────────────────────────────────
    /**
     * Task Detail view. Loads steps relationship.
     */
    public function show(Task $task)
    {
        $task->load('steps');
        return view('tasks.show', compact('task'));
    }

    // ─── LIVE SEARCH API ───────────────────────────────────────────────────────
    /**
     * JSON endpoint for the Command Palette live search.
     * SCOPED: only returns tasks owned by the authenticated user — never other users' data.
     *
     * @param  Request  $request  — expects ?q=search+term
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Query is scoped to auth()->id() — data privacy enforced at the DB level
        // Using auth()->user()->tasks() relationship ensures strict user isolation
        $tasks = auth()->user()->tasks()
            ->select('id', 'title', 'category', 'status', 'deadline')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->latest()
            ->limit(8)
            ->get();

        return response()->json($tasks);
    }
}
