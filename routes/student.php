<?php

use App\Http\Controllers\StudentAuth\GoogleAuthController;
use App\Http\Middleware\RequireStudentOrigin;
use App\Http\Middleware\UseStudentSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

// Student sessions use their own middleware stack (not the "web" group) and their own cookie.
Route::middleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    UseStudentSession::class,
    StartSession::class,
])->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

    Route::middleware([RequireStudentOrigin::class])->prefix('student')->group(function () {
        Route::get('/me', [GoogleAuthController::class, 'me']);
        Route::get('/admin', [GoogleAuthController::class, 'admin']);
        Route::post('/logout', [GoogleAuthController::class, 'logout']);
    });
});
