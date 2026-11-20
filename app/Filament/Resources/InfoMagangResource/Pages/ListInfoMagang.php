<?php

namespace App\Filament\Resources\InfoMagangResource\Pages;

use App\Filament\Resources\InfoMagangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInfoMagang extends ListRecords
{
    protected static string $resource = InfoMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
