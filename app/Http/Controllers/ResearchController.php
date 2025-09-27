<?php

namespace App\Http\Controllers;

use App\Models\Research;
use Illuminate\Http\JsonResponse;

class ResearchController extends Controller
{
    public function index(): JsonResponse
    {
        $research = Research::orderBy('created_at', 'desc')->get();

        return response()->json([
            'research' => $research,
        ]);
    }
}
