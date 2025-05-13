<?php

namespace App\Http\Controllers;

use App\Models\Syntax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyntaxController extends Controller
{
    public function index(): JsonResponse
    {
        $syntaxes = Syntax::orderBy('created_at', 'desc')->get();
        return response()->json([
            "syntaxes" => $syntaxes
        ]);
    }
}
