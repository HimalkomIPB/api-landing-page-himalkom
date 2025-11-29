<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PrestasiKategori extends Model
{
    protected $table = 'prestasi_category';

    protected $fillable = [
        'name',
    ];


    public function prestasis(): HasMany
    {
        return $this->hasMany(\App\Models\Prestasi::class, 'prestasi_kategori_id');
    }
}
