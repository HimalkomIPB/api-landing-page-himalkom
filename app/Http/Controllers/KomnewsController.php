<?php

namespace App\Http\Controllers;

use App\Models\Komnews;
use App\Models\KomnewsCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KomnewsController extends Controller
{
    public function index(): JsonResponse
    {
        $today = Carbon::today(config("app.timezone"));
        $categories = KomnewsCategory::all(["name", "slug"]);
        $komnews = Komnews::whereDate('created_at', '!=', $today)
            ->orderBy('created_at', 'desc')
            ->get();
        $todayHeadlines = Komnews::whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'categories' => $categories,
            'komnews' => $komnews,
            'todayHeadlines' => $todayHeadlines
        ]);
    }

    public function showBySlug(String $slug): JsonResponse
    {
        try {
            $komnews = Komnews::where("slug", $slug)->firstOrFail();
            return response()->json(['komnews' => $komnews]);
        } catch (ModelNotFoundException) {
            return response()->json(['errors' => 'Komnews not found'], 404);
        }
    }
}
