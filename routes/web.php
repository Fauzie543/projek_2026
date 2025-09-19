<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\EmployeeController;
use App\Http\Controllers\User\AttendanceController;

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});
// --- Employee Management (admin & manager) ---
Route::middleware(['auth', 'role_or_permission:admin|manage employees'])->group(function () {
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/data', [EmployeeController::class, 'data'])->name('employees.data');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    // Tambahkan route resource lainnya jika diperlukan (edit, update, destroy)
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});

// --- Attendance (admin, manager, staff sesuai permission) ---
Route::middleware(['auth', 'role_or_permission:admin|view attendance'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
});

// --- Attendance Edit (admin & staff yg punya izin edit) ---
Route::middleware(['auth', 'role_or_permission:admin|edit attendance'])->group(function () {
    Route::get('/attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
});

require __DIR__.'/auth.php';