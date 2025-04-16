<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Company Management - Super Admin only
    Route::middleware('role:Super_Admin')->group(function () {
        Route::get('/companies', [CompanyController::class, 'index']);
        Route::post('/companies', [CompanyController::class, 'store']);
    });
    
    // Expenses - accessible by all roles
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    
    // Expenses - Managers and Admins only
    Route::middleware('role:Admin,Manager')->group(function () {
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])
            ->middleware('same-company');
    });
    
    // Expenses - Admins only
    Route::middleware('role:Admin')->group(function () {
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
            ->middleware('same-company');
    });
    
    // User Management - Admins only
    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('same-company');
    });



    // Role Management - Super Admin
    Route::middleware('role:Super_Admin')->group(function () {
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole']);
    });

    // Role Management - Admin
    Route::middleware('role:Admin')->group(function () {
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])
            ->middleware('same-company');
    });
});