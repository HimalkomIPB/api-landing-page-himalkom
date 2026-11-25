<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegistrationInfoResource;
use App\Models\RegistrationInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

abstract class RegistrationInfoController extends Controller
{
    /** @var class-string<RegistrationInfo> */
    protected string $model;

    protected string $responseKey;

    protected string $notFoundMessage;

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['upcoming', 'active', 'closed'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $query = $this->model::withStatus($filters['status'] ?? null);

        if (filled($filters['q'] ?? null)) {
            $query->where('title', 'like', '%'.$filters['q'].'%');
        }

        $paginated = $query->latest()->orderByDesc('id')->paginate($filters['per_page'] ?? 6);

        return response()->json([
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            $this->responseKey => RegistrationInfoResource::collection($paginated->getCollection()),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $info = $this->model::where('slug', $slug)->first();

        if (! $info) {
            return response()->json(['errors' => $this->notFoundMessage], 404);
        }

        return response()->json([$this->responseKey => new RegistrationInfoResource($info)]);
    }
}
