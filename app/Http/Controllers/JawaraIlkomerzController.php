<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\JawaraIlkomerz;

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
