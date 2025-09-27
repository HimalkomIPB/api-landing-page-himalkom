<?php

namespace App\Http\Controllers;

use App\Models\Komnews;
use App\Models\KomnewsCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class KomnewsController extends Controller
{
    public function index(): JsonResponse
    {
        $today = Carbon::today(config('app.timezone'));
        $categories = KomnewsCategory::all(['name', 'slug']);
        $komnews = Komnews::whereDate('created_at', '!=', $today)
            ->orderBy('created_at', 'desc')
            ->get();
        $todayHeadlines = Komnews::whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'categories' => $categories,
            'komnews' => $komnews,
            'todayHeadlines' => $todayHeadlines,
        ]);
    }

    // For homepage, limit to 5 komnews
    public function indexHome(): JsonResponse
    {
        $today = Carbon::today(config('app.timezone'));
        $categories = KomnewsCategory::all(['name', 'slug']);
        $komnews = Komnews::whereDate('created_at', '!=', $today)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $todayHeadlines = Komnews::whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'categories' => $categories,
            'komnews' => $komnews,
            'todayHeadlines' => $todayHeadlines,
        ]);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $komnews = Komnews::where('slug', $slug)->firstOrFail();

            return response()->json(['komnews' => $komnews]);
        } catch (ModelNotFoundException) {
            return response()->json(['errors' => 'Komnews not found'], 404);
        }
    }
}
