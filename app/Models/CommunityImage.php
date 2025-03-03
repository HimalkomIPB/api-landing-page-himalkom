<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CommunityImage extends Model
{
    protected $guarded = ['id'];

    public function community(): BelongsTo {
        return $this->belongsTo(Community::class);
    }

    protected static function booted()
    {
        static::deleting(function ($cImage) {
            Storage::disk('public')->delete($cImage->image);
        });

        static::updating(function ($cImage) {
            if ($cImage->isDirty('url')) {
                $oldImage = $cImage->getOriginal('url');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}
