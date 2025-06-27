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
            ->get()
            ->map(function ($item) {
                $item->community_name = $item->community->name ?? 'Miscellaneous';
                return $item;
            });

        return response()->json(['jawaraIlkomerzs' => $jawaraIlkomerzs]);
    }
}
