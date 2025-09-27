<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Megaproker extends Model
{
    protected $guarded = ['id'];

    protected $with = ['images'];

    public function images(): HasMany
    {
        return $this->hasMany(MegaprokerImage::class);
    }

    protected static function booted()
    {
        static::deleting(function ($megaproker) {
            Storage::disk('public')->delete($megaproker->image);
        });

        static::updating(function ($megaproker) {
            if ($megaproker->isDirty('logo')) {
                $oldImage = $megaproker->getOriginal('logo');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}
