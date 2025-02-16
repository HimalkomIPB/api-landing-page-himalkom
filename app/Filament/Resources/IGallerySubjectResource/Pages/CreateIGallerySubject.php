<?php

namespace App\Filament\Resources\IGallerySubjectResource\Pages;

use App\Filament\Resources\IGallerySubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIGallerySubject extends CreateRecord
{
    protected static string $resource = IGallerySubjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
