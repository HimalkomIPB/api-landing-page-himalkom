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
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->community_name = $item->community->name ?? 'Miscellaneous';

                $now = Carbon::now();

                if ($item->start_date && $item->end_date) {
                    if ($now->lt(Carbon::parse($item->start_date))) {
                        $item->availability = 'not yet available';
                    } elseif ($now->between(Carbon::parse($item->start_date), Carbon::parse($item->end_date))) {
                        $item->availability = 'available';
                    } else {
                        $item->availability = 'overdue';
                    }
                } else {
                    $item->availability = 'unknown';
                }

                return $item;
            });

        return response()->json(['jawaraIlkomerzs' => $jawaraIlkomerzs]);
    }
}
