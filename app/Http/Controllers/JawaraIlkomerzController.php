<?php

namespace App\Http\Controllers;

use App\Models\JawaraIlkomerz;
use Illuminate\Http\Request;

class JawaraIlkomerzController extends Controller
{
    public function index()
    {
        $jawaraIlkomerzs = JawaraIlkomerz::with('community')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['jawaraIlkomerzs' => $jawaraIlkomerzs]);
    }
}
