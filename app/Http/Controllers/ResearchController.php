<?php

namespace App\Http\Controllers;

use App\Models\Research;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 6);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 6;

        $paginated = Research::orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        $research = $paginated->getCollection();

        $pagination = [
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
        ];

        return response()->json([
            'pagination' => $pagination,
            'research' => $research,
        ]);
    }
}
