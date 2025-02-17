<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
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

    protected static function booted()
    {
        static::creating(function ($division) {
            if (empty($news->slug)) {
                $division->slug = Str::slug($division->name);
            }
        });

        static::updating(function ($division) {
            $division->slug = Str::slug($division->name);
        });
    }
}
