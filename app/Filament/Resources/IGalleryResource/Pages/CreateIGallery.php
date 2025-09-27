<?php

namespace App\Filament\Resources\IGalleryResource\Pages;

use App\Filament\Resources\IGalleryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIGallery extends CreateRecord
{
    protected static string $resource = IGalleryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
