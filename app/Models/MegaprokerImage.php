<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MegaprokerImage extends Model
{
    protected $guarded = ['id'];

    public function megaproker(): BelongsTo
    {
        return $this->belongsTo(Megaproker::class);
    }

    protected static function booted()
    {
        static::deleting(function ($mImage) {
            Storage::disk('public')->delete($mImage->url);
        });

        static::updating(function ($mImage) {
            if ($mImage->isDirty('url')) {
                $oldImage = $mImage->getOriginal('url');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}
