<?php

namespace App\Filament\Resources\InfoBeasiswaResource\Pages;

use App\Filament\Resources\InfoBeasiswaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInfoBeasiswa extends ListRecords
{
    protected static string $resource = InfoBeasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
