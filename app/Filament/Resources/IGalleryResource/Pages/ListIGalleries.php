<?php

namespace App\Filament\Resources\IGalleryResource\Pages;

use App\Filament\Resources\IGalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIGalleries extends ListRecords
{
    protected static string $resource = IGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
