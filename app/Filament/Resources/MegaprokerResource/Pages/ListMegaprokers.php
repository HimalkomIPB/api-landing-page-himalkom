<?php

namespace App\Filament\Resources\MegaprokerResource\Pages;

use App\Filament\Resources\MegaprokerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMegaprokers extends ListRecords
{
    protected static string $resource = MegaprokerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
