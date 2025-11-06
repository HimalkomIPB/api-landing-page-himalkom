<?php

namespace App\Http\Controllers;

use App\Models\Syntax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyntaxController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 6);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 6;

        $paginated = Syntax::orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $pagination = [
            'current_page' => $paginated->currentPage(),
            'per_page'     => $paginated->perPage(),
            'total'        => $paginated->total(),
            'last_page'    => $paginated->lastPage(),
        ];
        $syntaxes = $paginated->getCollection();

        return response()->json([
            'syntaxes' => $syntaxes,
            'pagination' => $pagination
        ]);
    }
}
