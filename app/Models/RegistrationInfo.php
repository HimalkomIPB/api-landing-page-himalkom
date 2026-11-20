<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class RegistrationInfo extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
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

    public function getStatusAttribute(): string
    {
        $today = today(config('app.timezone'));

        if ($this->start_date->greaterThan($today)) {
            return 'upcoming';
        }

        return $this->end_date->lessThan($today) ? 'closed' : 'active';
    }

    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        $today = today(config('app.timezone'))->toDateString();

        return match ($status) {
            'upcoming' => $query->whereDate('start_date', '>', $today),
            'active' => $query->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today),
            'closed' => $query->whereDate('end_date', '<', $today),
            default => $query,
        };
    }

    protected static function booted(): void
    {
        static::creating(function (RegistrationInfo $info) {
            $prefix = Str::slug(Str::limit($info->title, 180, '')) ?: Str::slug($info->getTable());
            $info->slug = $prefix.'-'.Str::lower((string) Str::ulid());
        });

        static::updated(function (RegistrationInfo $info) {
            if ($info->wasChanged('image')) {
                $info->deleteCoverAfterCommit($info->getRawOriginal('image'));
            }
        });

        static::deleted(function (RegistrationInfo $info) {
            $info->deleteCoverAfterCommit($info->getRawOriginal('image'));
        });
    }

    private function deleteCoverAfterCommit(?string $path): void
    {
        if ($path) {
            $this->getConnection()->afterCommit(fn () => Storage::disk('public')->delete($path));
        }
    }
}
