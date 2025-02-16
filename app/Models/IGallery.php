<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IGallery extends Model
{
    protected $fillable = [
        "name",
        "description",
        "contributor",
        "angkatan",
        "image",
        "subject_id",
        "link"
    ];

    protected $with = ["subject"];

    public function subject()
    {
        return $this->belongsTo(IGallerySubject::class);
    }

    protected static function booted()
    {
        static::deleting(function ($syntax) {
            Storage::disk('public')->delete($syntax->image);
        });

        static::updating(function ($syntax) {
            if ($syntax->isDirty('image')) {
                $oldImage = $syntax->getOriginal('image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}
