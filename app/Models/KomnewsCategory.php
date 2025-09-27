<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KomnewsCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function komnews()
    {
        return $this->belongsToMany(Komnews::class, 'komnews_category_relations');
    }

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}
