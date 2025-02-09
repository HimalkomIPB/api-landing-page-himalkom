<?php

use App\Http\Controllers\KomnewsController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\SyntaxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Komnews
Route::get('/komnews', [KomnewsController::class, 'index']);
Route::get('/komnews/{slug}', [KomnewsController::class, 'showBySlug']);

// Research
Route::get("/research", [ResearchController::class, "index"]);

// Syntax
Route::get("/syntaxes", [SyntaxController::class, "index"]);
