<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\KomnewsCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Komnews extends Model
{
    protected $fillable = [
        "title",
        "slug",
        "content",
        "excerp",
        "image"
    ];

    protected $with = ['categories'];

    public function categories()
    {
        return $this->belongsToMany(KomnewsCategory::class, 'komnews_category_relations')->select(["name", "slug"]);
    }

    protected static function booted()
    {
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = self::setSlug($news->title);
            }

            if (empty($news->excerp)) {
                $news->excerp = self::setExcerp($news->content);
            }
        });

        static::deleting(function ($news) {
            Storage::disk('public')->delete($news->image);
        });

        static::updating(function ($news) {
            $news->slug = self::setSlug($news->title);
            $news->excerp = self::setExcerp($news->content);

            if ($news->isDirty('image')) {
                $oldImage = $news->getOriginal('image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }

    static function setExcerp($content): String
    {
        $excerp = Str::limit(strip_tags($content), $limit = 50, $end = "...");
        $excerp = str_replace("&nbsp;", "", $excerp);
        $excerp = $excerp . "...";
        return $excerp;
    }

    static function setSlug($title): String
    {
        return Str::slug($title) . '-' . time() . '-' . bin2hex(random_bytes(8));
    }
}
