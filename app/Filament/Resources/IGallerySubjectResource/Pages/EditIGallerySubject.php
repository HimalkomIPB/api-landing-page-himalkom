<?php

namespace App\Filament\Resources\IGallerySubjectResource\Pages;

use App\Filament\Resources\IGallerySubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIGallerySubject extends EditRecord
{
    protected static string $resource = IGallerySubjectResource::class;

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
