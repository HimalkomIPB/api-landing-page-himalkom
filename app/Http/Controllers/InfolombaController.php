<?php

namespace App\Http\Controllers;

use App\Http\Resources\InfolombaResource;
use App\Models\Infolomba;
use App\Models\InfolombaCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InfolombaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:infolomba_categories,id'],
            'status' => ['nullable', Rule::in(['upcoming', 'active', 'closed'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $today = today(config('app.timezone'))->toDateString();
        $query = Infolomba::with('category');

        if (filled($filters['q'] ?? null)) {
            $query->where('title', 'like', '%'.$filters['q'].'%');
        }

        if (filled($filters['category_id'] ?? null)) {
            $query->where('infolomba_category_id', $filters['category_id']);
        }

        match ($filters['status'] ?? null) {
            'upcoming' => $query->whereDate('start_date', '>', $today),
            'active' => $query->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today),
            'closed' => $query->whereDate('end_date', '<', $today),
            default => null,
        };

        $paginated = $query->latest()->orderByDesc('id')
            ->paginate($filters['per_page'] ?? 6)->withQueryString();

        return response()->json([
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            'categories' => InfolombaCategory::orderBy('name')->get(['id', 'name']),
            'infolomba' => InfolombaResource::collection($paginated->getCollection()),
        ]);
    }

    public function categories(): JsonResponse
    {
        return response()->json([
            'categories' => InfolombaCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $infolomba = Infolomba::with('category')->where('slug', $slug)->first();

        if (! $infolomba) {
            return response()->json(['errors' => 'Info lomba not found'], 404);
        }

        return response()->json(['infolomba' => new InfolombaResource($infolomba)]);
    }
}
