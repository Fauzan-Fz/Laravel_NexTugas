<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// === RUTE GUEST (Hanya bisa diakses jika BELUM login) ===
// Middleware 'guest' memastikan user yg sudah login tidak bisa membuka halaman login lagi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register routes
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// === RUTE TERLINDUNGI (Wajib Login) ===
// Penjelasan Keamanan Session Laravel 13:
// Middleware 'auth' memeriksa keberadaan sesi yang valid pada setiap request.
// Jika cookie session tidak ditemukan/kadaluarsa, user diarahkan kembali ke rute 'login'.
// Ini menjamin bahwa hanya pengguna terautentikasi yang bisa mengakses data sensitif.
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard — shows all tasks with stats and native calendar
    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');

    // All tasks — dedicated view
    Route::get('/all', [TaskController::class, 'all'])->name('all');

    // Completed tasks — dedicated checklist view
    Route::get('/completed', [TaskController::class, 'completed'])->name('completed');

    // Urgent tasks — dedicated view
    Route::get('/urgent', [TaskController::class, 'urgent'])->name('urgent');

    // Task CRUD
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // AI sub-task step toggle
    Route::patch('/steps/{step}/toggle', [TaskController::class, 'toggleStep'])->name('steps.toggle');

    // Dynamic subject view — {subject} is the URL-encoded school subject string (e.g. "Mathematics", "Web Programming")
    Route::get('/subjects/{subject}', [TaskController::class, 'subject'])->name('subjects.show');

    // Live search — JSON endpoint for Command Palette; scoped to auth()->id() for data privacy
    Route::get('/tasks/search', [TaskController::class, 'search'])->name('tasks.search');

    // Task detail view — {task} uses Laravel route model binding, auto-resolves Task by primary key
    // NOTE: Must come AFTER /tasks/search so 'search' isn't treated as a {task} parameter
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
});
