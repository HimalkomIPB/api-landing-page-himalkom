<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class JawaraIlkomerz extends Model
{
    protected $guarded = ['id'];

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->image && Storage::disk('public')->exists($model->image)) {
                Storage::disk('public')->delete($model->image);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('image')) {
                $oldImage = $model->getOriginal('image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}
