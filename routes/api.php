<?php

use App\Http\Controllers\KomnewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/komnews', [KomnewsController::class, 'index']);
Route::get('/komnews/{komnews:slug}', [KomnewsController::class, 'showBySlug']);