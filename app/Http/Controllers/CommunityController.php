<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityPortofolio;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $communities = Community::select('name', 'slug', 'logo', 'description')->get();

        return response()->json([
            'communities' => $communities,
        ]);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $community = Community::with(['images'])->where('slug', $slug)->firstOrFail();

            return response()->json(['community' => $community]);
        } catch (ModelNotFoundException) {
            return response()->json(['errors' => 'Community not found'], 404);
        }
    }

    public function indexPortofolio(string $slug): JsonResponse
    {
        try {
            $communityPortofolios = CommunityPortofolio::whereHas('community', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })->get();

            return response()->json(['communityPortofolios' => $communityPortofolios]);
        } catch (ModelNotFoundException) {
            return response()->json(['errors' => 'Community not found or has no portfolios'], 404);
        }
    }
}
