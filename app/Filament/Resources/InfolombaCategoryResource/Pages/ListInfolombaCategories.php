<?php

namespace App\Filament\Resources\InfolombaCategoryResource\Pages;

use App\Filament\Resources\InfolombaCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfolombaCategories extends ListRecords
{
    protected static string $resource = InfolombaCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
