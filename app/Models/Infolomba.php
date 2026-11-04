<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Infolomba extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'infolomba_category_id',
        'start_date',
        'end_date',
        'link',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InfolombaCategory::class, 'infolomba_category_id');
    }

    public function getStatusAttribute(): string
    {
        $today = today(config('app.timezone'));

        if ($this->start_date->greaterThan($today)) {
            return 'upcoming';
        }

        return $this->end_date->lessThan($today) ? 'closed' : 'active';
    }

    protected static function booted(): void
    {
        static::creating(function (Infolomba $infolomba) {
            // Keep detail links stable when the title is edited, including duplicate titles.
            $prefix = Str::slug(Str::limit($infolomba->title, 180, '')) ?: 'lomba';
            $infolomba->slug = $prefix.'-'.Str::lower((string) Str::ulid());
        });

        static::updated(function (Infolomba $infolomba) {
            if ($infolomba->wasChanged('image')) {
                $oldImage = $infolomba->getOriginal('image');

                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        static::deleted(function (Infolomba $infolomba) {
            if ($infolomba->image) {
                Storage::disk('public')->delete($infolomba->image);
            }
        });
    }
}
