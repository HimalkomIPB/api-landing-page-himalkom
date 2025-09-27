<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Community extends Model
{
    protected $guarded = ['id'];

    public function images(): HasMany
    {
        return $this->hasMany(CommunityImage::class);
    }

    public function portofolios(): HasMany
    {
        return $this->hasMany(CommunityPortofolio::class);
    }

    public function jawaraIlkomerzs(): HasMany
    {
        return $this->hasMany(JawaraIlkomerz::class);
    }

    protected $casts = [
        'purposes' => 'array',
        'achievements' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($community) {
            if (empty($community->slug)) {
                $community->slug = Str::slug($community->name);
            }
        });

        static::deleting(function ($community) {
            Storage::disk('public')->delete($community->logo);
        });

        static::updating(function ($community) {
            $community->slug = Str::slug($community->name);

            if ($community->isDirty('logo')) {
                $oldImage = $community->getOriginal('logo');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}
