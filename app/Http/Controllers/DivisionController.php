<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class DivisionController extends Controller
{
    public function index(): JsonResponse
    {
        $divisions = Division::orderBy('name', 'asc')->get();

        return response()->json([
            'divisions' => $divisions,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $division = Division::with(['staff', 'workPrograms'])->where('slug', $slug)->firstOrFail();

            return response()->json(['division' => $division]);
        } catch (ModelNotFoundException) {
            return response()->json(['errors' => 'division not found'], 404);
        }
    }
}
