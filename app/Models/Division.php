<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $guarded = [
        "id"
    ];

    public function staff(): HasMany
    {
        return  $this->hasMany(Staff::class);
    }

    public function workPrograms(): HasMany
    {
        return $this->hasMany(DivisionWorkProgram::class);
    }

    protected static function booted()
    {
        static::creating(function ($division) {
            if (empty($news->slug)) {
                $division->slug = Str::slug($division->name);
            }
        });

        static::deleting(function ($division) {
            Storage::disk('public')->delete($division->logo);
        });

        static::updating(function ($division) {
            $division->slug = Str::slug($division->name);
            if ($division->isDirty('logo')) {
                $oldImage = $division->getOriginal('logo');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}
