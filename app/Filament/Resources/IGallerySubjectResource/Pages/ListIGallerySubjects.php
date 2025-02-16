<?php

namespace App\Filament\Resources\IGallerySubjectResource\Pages;

use App\Filament\Resources\IGallerySubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIGallerySubjects extends ListRecords
{
    protected static string $resource = IGallerySubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
