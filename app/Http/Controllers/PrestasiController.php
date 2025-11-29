<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use App\Models\PrestasiKategori;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    /**
     * GET /api/prestasis
     * Query params:
     *  - q       : search text (nama, deskripsi, penyelenggara, kategori)
     *  - tahun   : filter tahun (YYYY)
     *  - per_page: pagination size (default 10, max 100)
     *  - page    : page number
     *  - sort    : kolom sorting (allowed: tahun, created_at, nama)
     *  - dir     : asc|desc
     */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $tahun = $request->query('tahun');

        // default per page = 6
        $perPage = (int) $request->query('per_page', 6);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 6;

        $sort = $request->query('sort', 'tahun');
        $dir = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = Prestasi::query();

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('nama', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhere('penyelenggara', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($request->kategori && $request->kategori !== 'all') {
            $query->whereHas('prestasiKategori', function ($q) use ($request) {
                $q->where('name', $request->kategori);
            });
        }

        $allowedSorts = ['tahun', 'created_at', 'nama'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'tahun';
        }

        $paginated = $query->orderBy($sort, $dir)->paginate($perPage)->withQueryString();

        $items = $paginated->getCollection()->map(function (Prestasi $p) {
            return [
                'id' => $p->id,
                'nama' => $p->nama,
                'tahun' => (string) $p->tahun,
                'deskripsi' => $p->deskripsi,
                'penyelenggara' => $p->penyelenggara,
                'lokasi' => $p->lokasi,
                'kategori' => $p->prestasiKategori ? [
                    'id' => $p->prestasiKategori->id,
                    'name' => $p->prestasiKategori->name,
                ] : null,
                'bukti_path' => $p->bukti_path,
                'bukti_url' => $p->bukti_url,
                'created_at' => $p->created_at?->toDateTimeString(),
                'updated_at' => $p->updated_at?->toDateTimeString(),
            ];
        })->toArray();

        $allkategori = PrestasiKategori::select('id', 'name')->orderBy('name')->get();
        $pagination = [
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
        ];
        $allYears = Prestasi::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return response()->json([
            'pagination' => $pagination,
            'prestasi' => $items,
            'all_kategori' => $allkategori,
            'all_years' => $allYears,
        ]);
    }

    /**
     * GET /api/prestasis/{id}
     * detail item
     */
    public function show($id)
    {
        $p = Prestasi::find($id);

        if (! $p) {
            return response()->json([
                'status' => 'error',
                'message' => 'Prestasi not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'prestasi' => [
                'id' => $p->id,
                'nama' => $p->nama,
                'tahun' => (string) $p->tahun,
                'deskripsi' => $p->deskripsi,
                'penyelenggara' => $p->penyelenggara,
                'lokasi' => $p->lokasi,
                'kategori' => $p->kategori,
                'bukti_path' => $p->bukti_path,
                'bukti_url' => $p->bukti_url,
                'created_at' => $p->created_at?->toDateTimeString(),
                'updated_at' => $p->updated_at?->toDateTimeString(),
            ],
        ]);
    }
}
