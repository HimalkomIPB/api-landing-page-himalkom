<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DivisionController extends Controller
{
    public function index(): JsonResponse
    {
        $divisions = Division::all();
        return response()->json([
            "divisions" => $divisions
        ]);
    }

    public function showStaffPerDivision(String $slug): JsonResponse
    {
        try {
            $division = Division::with("staff")->where("slug", $slug)->firstOrFail();
            return response()->json(['division' => $division]);
        } catch (ModelNotFoundException) {
            return response()->json(['errors' => 'division not found'], 404);
        }
    }
}
