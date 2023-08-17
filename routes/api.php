<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
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

Route::get('/home', [HomeController::class, 'index'])->name('index');

Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/verify', [AuthController::class, 'verify'])->name('verify');
    Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode'])->name('send-verification-code');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('show-reset-password');
    Route::post('/forget-password', [AuthController::class, 'forgetPassword'])->name('forget-password');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
