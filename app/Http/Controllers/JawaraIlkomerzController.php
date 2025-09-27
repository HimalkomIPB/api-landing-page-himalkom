<?php

namespace App\Http\Controllers;

use App\Models\JawaraIlkomerz;
use Carbon\Carbon;

class JawaraIlkomerzController extends Controller
{
    public function index()
    {
        $jawaraIlkomerzs = JawaraIlkomerz::with('community')
            ->where('end_date', '>=', Carbon::now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['jawaraIlkomerzs' => $jawaraIlkomerzs]);
    }
}
