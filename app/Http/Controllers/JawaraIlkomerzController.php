<?php

namespace App\Http\Controllers;

use App\Models\JawaraIlkomerz;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JawaraIlkomerzController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $today = Carbon::now()->toDateString();
        $perPage = (int) $request->query('per_page', 6);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 6;

        $query = JawaraIlkomerz::with('community')
            ->where('end_date', '>=', $today)
            ->orderBy('created_at', 'desc');
        $paginated = $query->paginate($perPage)->withQueryString();
        $jawaraIlkomerzs = $paginated->getCollection();
        $pagination = [
            'current_page' => $paginated->currentPage(),
            'per_page'     => $paginated->perPage(),
            'total'        => $paginated->total(),
            'last_page'    => $paginated->lastPage(),
        ];
        return response()->json([
            'jawaraIlkomerzs' => $jawaraIlkomerzs,
            'pagination' => $pagination,
        ]);
    }
}
