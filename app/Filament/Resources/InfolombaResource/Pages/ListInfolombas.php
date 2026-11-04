<?php

namespace App\Filament\Resources\InfolombaResource\Pages;

use App\Filament\Resources\InfolombaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfolombas extends ListRecords
{
    protected static string $resource = InfolombaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
