<?php

namespace App\Http\Controllers;

use App\Models\IGallerySubject;
use Illuminate\Http\JsonResponse;

class IGalleryController extends Controller
{
    public function index(): JsonResponse
    {
        $igalleries = IGallerySubject::with(['iGalleries'])->get();

        return response()->json([
            'igalleries' => $igalleries,
        ]);
    }
}
