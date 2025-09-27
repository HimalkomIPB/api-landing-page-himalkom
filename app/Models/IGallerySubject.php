<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IGallerySubject extends Model
{
    protected $fillable = [
        'name',
    ];

    public function iGalleries()
    {
        return $this->hasMany(IGallery::class, 'subject_id');
    }
}
