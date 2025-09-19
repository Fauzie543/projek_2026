<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\EmployeeController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\StockController;
use App\Http\Controllers\Procurement\PurchaseRequestController;
use App\Http\Controllers\Procurement\PurchaseOrderController;
use App\Http\Controllers\Procurement\ReceivingController;

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

// --- Users (Admin Only) ---
Route::middleware(['auth', 'role:admin'])->prefix('users')->as('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/data', [UserController::class, 'data'])->name('data');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

// --- Employee Management (Admin & Manager) ---
Route::middleware(['auth', 'role_or_permission:admin|manage employees'])
    ->prefix('employees')->as('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/data', [EmployeeController::class, 'data'])->name('data');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });

// --- Attendance ---
Route::middleware(['auth', 'role_or_permission:admin|view attendance'])
    ->prefix('attendance')->as('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
    });

Route::middleware(['auth', 'role_or_permission:admin|edit attendance'])
    ->prefix('attendance')->as('attendance.')->group(function () {
        Route::get('/{id}/edit', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AttendanceController::class, 'update'])->name('update');
    });

// --- Inventory ---
Route::middleware(['auth', 'role_or_permission:admin|manage inventory'])
    ->prefix('inventory')->as('inventory.')->group(function () {
        // Items
        Route::prefix('items')->as('items.')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('index');
            Route::get('/create', [ItemController::class, 'create'])->name('create');
            Route::post('/', [ItemController::class, 'store'])->name('store');
            Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [ItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy');
        });

        // Categories
        Route::prefix('categories')->as('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        // Suppliers
        Route::prefix('suppliers')->as('suppliers.')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('index');
            Route::get('/create', [SupplierController::class, 'create'])->name('create');
            Route::post('/', [SupplierController::class, 'store'])->name('store');
            Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
            Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
            Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        });

        // Stocks
        Route::prefix('stocks')->as('stocks.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/history', [StockController::class, 'history'])->name('history');
            Route::post('/in', [StockController::class, 'stockIn'])->name('in');
            Route::post('/out', [StockController::class, 'stockOut'])->name('out');
        });
    });

// --- Procurement ---
Route::middleware(['auth', 'role_or_permission:admin|manage procurement'])
    ->prefix('procurement')->as('procurement.')->group(function () {
        // Purchase Requests
        Route::prefix('purchase-requests')->as('purchase-requests.')->group(function () {
            Route::get('/', [PurchaseRequestController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseRequestController::class, 'create'])->name('create');
            Route::post('/', [PurchaseRequestController::class, 'store'])->name('store');
            Route::get('/{pr}/edit', [PurchaseRequestController::class, 'edit'])->name('edit');
            Route::put('/{pr}', [PurchaseRequestController::class, 'update'])->name('update');
            Route::delete('/{pr}', [PurchaseRequestController::class, 'destroy'])->name('destroy');
        });

        // Purchase Orders
        Route::prefix('purchase-orders')->as('purchase-orders.')->group(function () {
            Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
            Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
            Route::get('/{po}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
            Route::put('/{po}', [PurchaseOrderController::class, 'update'])->name('update');
            Route::delete('/{po}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
        });

        // Receiving
        Route::prefix('receivings')->as('receivings.')->group(function () {
            Route::get('/', [ReceivingController::class, 'index'])->name('index');
            Route::get('/create', [ReceivingController::class, 'create'])->name('create');
            Route::post('/', [ReceivingController::class, 'store'])->name('store');
            Route::get('/{receiving}/edit', [ReceivingController::class, 'edit'])->name('edit');
            Route::put('/{receiving}', [ReceivingController::class, 'update'])->name('update');
            Route::delete('/{receiving}', [ReceivingController::class, 'destroy'])->name('destroy');
        });
    });

require __DIR__.'/auth.php';