<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Prestasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tahun',
        'deskripsi',
        'penyelenggara',
        'lokasi',
        'prestasi_kategori_id',
        'bukti_path',
    ];

    public function prestasiKategori()
    {
        return $this->belongsTo(\App\Models\PrestasiKategori::class, 'prestasi_kategori_id');
    }

    protected static function booted()
    {
        static::deleting(function (Prestasi $prestasi) {
            if ($prestasi->bukti_path && Storage::disk('public')->exists($prestasi->bukti_path)) {
                Storage::disk('public')->delete($prestasi->bukti_path);
            }
        });

        static::updating(function (Prestasi $prestasi) {
            if ($prestasi->isDirty('bukti_path')) {
                $old = $prestasi->getOriginal('bukti_path');
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
            }
        });
    }

    public function getBuktiUrlAttribute()
    {
        if (! $this->bukti_path) {
            return null;
        }

        $dirname = pathinfo($this->bukti_path, PATHINFO_DIRNAME);
        $basename = rawurlencode(pathinfo($this->bukti_path, PATHINFO_BASENAME));
        $encodedPath = $dirname && $dirname !== '.' ? ($dirname.'/'.$basename) : $basename;

        return Storage::disk('public')->url($encodedPath);
    }
}
