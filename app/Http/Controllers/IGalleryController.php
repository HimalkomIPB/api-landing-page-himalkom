<?php

namespace App\Http\Controllers;

use App\Models\IGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IGalleryController extends Controller
{
    public function index(): JsonResponse
    {
        $igalleries = IGallery::all();
        return response()->json([
            "igalleries" => $igalleries
        ]);
    }
}
