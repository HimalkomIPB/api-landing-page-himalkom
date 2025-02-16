<?php

namespace App\Filament\Resources\IGalleryResource\Pages;

use App\Filament\Resources\IGalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIGallery extends EditRecord
{
    protected static string $resource = IGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
