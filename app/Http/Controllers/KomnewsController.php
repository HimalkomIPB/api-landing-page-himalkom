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
    public function index(Request $request): JsonResponse
    {
        $today = Carbon::today(config('app.timezone'));
        $categories = KomnewsCategory::all(['name', 'slug']);
        $perPage = (int) $request->query('per_page', 6);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 6;
        $query = Komnews::whereDate('created_at', '!=', $today);
        $paginated = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        $komnews = $paginated->getCollection();
        $today = Carbon::today();
        $eightDaysAgo = $today->copy()->subDays(7); // total 8 days = today + 7 days before
        $headlines = Komnews::whereBetween('created_at', [$eightDaysAgo, $today->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();
        $pagination = [
            'current_page' => $paginated->currentPage(),
            'per_page'     => $paginated->perPage(),
            'total'        => $paginated->total(),
            'last_page'    => $paginated->lastPage(),
        ];


        return response()->json([
            'pagination' => $pagination,
            'categories' => $categories,
            'komnews' => $komnews,
            'todayHeadlines' => $headlines,
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
