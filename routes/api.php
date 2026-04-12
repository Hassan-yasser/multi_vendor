<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
| مسارات JSON تحت /api/auth/* — middleware web للجلسة (نفس الكوكي مع الويب).
*/
Route::prefix('auth')
    ->middleware(['web', 'throttle:60,1'])
    ->group(function (): void {
        Route::middleware('guest')->group(function (): void {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('register', [AuthController::class, 'register']);
            Route::post('forgot-password', [AuthController::class, 'sendResetLink']);
            Route::post('reset-password', [AuthController::class, 'resetPassword']);
        });

        Route::middleware('auth')->group(function (): void {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });
