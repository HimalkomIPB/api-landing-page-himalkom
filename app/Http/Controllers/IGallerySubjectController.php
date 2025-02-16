<?php

namespace App\Http\Controllers;

use App\Models\IGallerySubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IGallerySubjectController extends Controller
{
    public function index(): JsonResponse
    {
        $igallerySubjects = IGallerySubject::all();
        return response()->json([
            "igallery_subjects" => $igallerySubjects
        ]);
    }
}
