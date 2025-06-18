<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ResourceController; // Thêm ResourceController

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route cho trang Welcome
Route::get('/', function () {
    return view('welcome');
});

// Route cho Dashboard, yêu cầu xác thực và xác minh
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Nhóm các route yêu cầu xác thực người dùng
Route::middleware('auth')->group(function () {

    // --- Profile Routes ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Event/Calendar Routes ---
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar.layout');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/api/events', [EventController::class, 'getEvents'])->name('api.events.index');

    // --- Task Routes ---
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('tasks/{task}/toggle-completed', [TaskController::class, 'toggleCompleted'])->name('tasks.toggleCompleted');
    Route::post('tasks/destroy-all', [TaskController::class, 'destroyAll'])->name('tasks.destroyAll');

    // --- Task List Routes (sử dụng ListController) ---
    Route::post('/task-lists', [ListController::class, 'store'])->name('task-lists.store');
    Route::put('/task-lists/{list}', [ListController::class, 'update'])->name('task-lists.update');
    Route::delete('/task-lists/{list}', [ListController::class, 'destroy'])->name('task-lists.destroy');

    // --- Resource Library Routes ---
    // Hiển thị trang thư viện tài nguyên
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    // API để lấy danh sách tài nguyên (dùng cho AJAX/Fetch)
    Route::get('/api/resources', [ResourceController::class, 'getResources'])->name('api.resources.index');
    // API để thêm tài nguyên mới
    Route::post('/api/resources', [ResourceController::class, 'store'])->name('api.resources.store');
    // API để cập nhật trạng thái của tài nguyên
    Route::patch('/api/resources/{resource}/status', [ResourceController::class, 'updateStatus'])->name('api.resources.updateStatus');
    // Route để mở URL tài nguyên
    Route::get('/resources/{resource}/open', [ResourceController::class, 'openUrl'])->name('resources.open');
    // API để xóa tài nguyên
    Route::delete('/api/resources/{resource}', [ResourceController::class, 'destroy'])->name('api.resources.destroy');
    // API để cập nhật tài nguyên
    Route::put('/api/resources/{resource}', [ResourceController::class, 'update'])->name('api.resources.update');

    // Nếu bạn muốn hỗ trợ chỉnh sửa/xóa tài nguyên từ backend:
    // Route::put('/api/resources/{resource}', [ResourceController::class, 'update'])->name('api.resources.update');
    // Route::delete('/api/resources/{resource}', [ResourceController::class, 'destroy'])->name('api.resources.destroy');

});

require __DIR__.'/auth.php';