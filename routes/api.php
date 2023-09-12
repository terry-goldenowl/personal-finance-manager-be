<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CategoriesController;
use App\Http\Controllers\Api\v1\CategoryPlansController;
use App\Http\Controllers\Api\v1\MonthPlansController;
use App\Http\Controllers\Api\v1\PlansController;
use App\Http\Controllers\Api\v1\ReportsController;
use App\Http\Controllers\Api\v1\TransactionsController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\WalletsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode'])->name('send-verification-code');

    Route::middleware('guest')->group(function () {
        // Authentication
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/verify', [AuthController::class, 'verify'])->name('verify');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('show-reset-password');
        Route::post('/forget-password', [AuthController::class, 'forgetPassword'])->name('forget-password');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Categories
        Route::post('/categories', [CategoriesController::class, 'create'])->name('create-category');
        Route::get('/categories', [CategoriesController::class, 'get'])->name('get-categories');
        Route::patch('/categories/{id}', [CategoriesController::class, 'update'])->name('update-category');
        Route::delete('/categories/{id}', [CategoriesController::class, 'delete'])->name('delete-category');

        // Wallets
        Route::post('/wallets', [WalletsController::class, 'create'])->name('create-wallet');
        Route::get('/wallets', [WalletsController::class, 'get'])->name('get-wallets');
        Route::patch('/wallets/{id}', [WalletsController::class, 'update'])->name('update-wallet');
        Route::delete('/wallets/{id}', [WalletsController::class, 'delete'])->name('delete-wallet');

        // Transactions
        Route::post('/transactions', [TransactionsController::class, 'create'])->name('create-transaction');
        Route::get('/transactions', [TransactionsController::class, 'get'])->name('get-transactions');
        Route::patch('/transactions/{id}', [TransactionsController::class, 'update'])->name('update-transactions');
        Route::delete('/transactions/{id}', [TransactionsController::class, 'delete'])->name('delete-transactions');

        // Reports
        Route::get('/reports', [ReportsController::class, 'get'])->name('get-reports');

        // Month Plans
        Route::post('/plans/month', [MonthPlansController::class, 'create'])->name('create-month-plan');
        Route::get('/plans/month', [MonthPlansController::class, 'get'])->name('get-month-plans');
        Route::patch('/plans/month/{id}', [MonthPlansController::class, 'update'])->name('update-month-plan');
        Route::delete('/plans/month/{id}', [MonthPlansController::class, 'delete'])->name('delete-month-plan');

        // Category Plans
        Route::post('/plans/category', [CategoryPlansController::class, 'create'])->name('create-category-plan');
        Route::get('/plans/category', [CategoryPlansController::class, 'get'])->name('get-category-plans');
        Route::patch('/plans/category/{id}', [CategoryPlansController::class, 'update'])->name('update-category-plan');
        Route::delete('/plans/category/{id}', [CategoryPlansController::class, 'delete'])->name('delete-category-plan');

        // Users
        Route::get('/users', [UserController::class, 'getAll'])->name('get-users');
        Route::patch('/users', [UserController::class, 'updateUser'])->name('update-user');
        Route::patch('/users/update-password', [UserController::class, 'updatePassword'])->name('update-password');
        Route::delete('/users', [UserController::class, 'deleteUser'])->name('delete-user');
    });
});
