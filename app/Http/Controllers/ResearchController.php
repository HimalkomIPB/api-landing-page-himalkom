<?php

namespace App\Http\Controllers;

use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResearchController extends Controller
{
    public function index(): JsonResponse
    {
        $research = Research::all();
        return response()->json([
            "research" => $research
        ]);
    }
}
