<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class InfolombaCategory extends Model
{
    protected $fillable = ['name'];

    public function infolombas(): HasMany
    {
        return $this->hasMany(Infolomba::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (InfolombaCategory $category) {
            if ($category->infolombas()->exists()) {
                throw ValidationException::withMessages([
                    'name' => 'Kategori masih digunakan. Pindahkan atau hapus info lomba terkait terlebih dahulu.',
                ]);
            }
        });
    }
}
