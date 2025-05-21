<?php

use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\IGalleryController;
use App\Http\Controllers\IGallerySubjectController;
use App\Http\Controllers\KomnewsController;
use App\Http\Controllers\MegaprokerController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\SyntaxController;
use App\Http\Middleware\RestrictToFrontendDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([RestrictToFrontendDomain::class])->group(function () {
    //community
    Route::get('/communities', [CommunityController::class, 'index']);
    Route::get('/communities/{slug}', [CommunityController::class, 'showBySlug']);
    Route::get('/communities/{slug}/portofolio', [CommunityController::class, 'indexPortofolio']);

    //division
    Route::get("/divisions", [DivisionController::class, 'index']);
    Route::get("/divisions/{slug}", [DivisionController::class, 'show']);

    // iGallery
    Route::get("/igalleries", [IGalleryController::class, 'index']);
    Route::get("/igalleries/subjects", [IGallerySubjectController::class, 'index']);

    // Komnews
    Route::get('/komnews', [KomnewsController::class, 'index']);
    Route::get('/komnews/{slug}', [KomnewsController::class, 'showBySlug']);

    // Megaproker
    Route::get('/megaprokers', [MegaprokerController::class, 'index']);

    // Research
    Route::get("/research", [ResearchController::class, "index"]);

    // Syntax
    Route::get("/syntaxes", [SyntaxController::class, "index"]);
});
