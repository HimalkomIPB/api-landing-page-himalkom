<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Komnews extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerp',
        'image',
    ];

    protected $with = ['categories'];

    public function categories()
    {
        return $this->belongsToMany(KomnewsCategory::class, 'komnews_category_relations');
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

            $doc = new \DOMDocument;
            libxml_use_internal_errors(true);
            $doc->loadHTML($news->content);

            $images = $doc->getElementsByTagName('img');

            foreach ($images as $img) {
                $src = $img->getAttribute('src');

                $relativePath = str_replace(url('/storage'), '', $src);

                Storage::disk('public')->delete($relativePath);
            }
            libxml_clear_errors();
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

    public static function setExcerp($content): string
    {
        $excerp = Str::limit(strip_tags($content), $limit = 50, $end = '...');
        $excerp = str_replace('&nbsp;', '', $excerp);
        $excerp = $excerp.'...';

        return $excerp;
    }

    public static function setSlug($title): string
    {
        return Str::slug($title).'-'.time().'-'.bin2hex(random_bytes(8));
    }
}
