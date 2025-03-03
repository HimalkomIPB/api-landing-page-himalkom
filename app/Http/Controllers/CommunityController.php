<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $communities = Community::select('name', 'slug', 'logo')->get();
        return response()->json([
            'communities' => $communities
        ]);
    }

    public function showBySlug(String $slug): JsonResponse
    {
        try {
            $community = Community::with(['images'])->where("slug", $slug)->firstOrFail();
            return response()->json(['community' => $community]);
        } catch (ModelNotFoundException) {
            return response()->json(['errors' => 'Community not found'], 404);
        }
    }
}
