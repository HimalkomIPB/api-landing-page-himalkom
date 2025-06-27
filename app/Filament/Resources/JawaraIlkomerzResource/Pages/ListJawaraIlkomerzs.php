<?php

namespace App\Filament\Resources\JawaraIlkomerzResource\Pages;

use App\Filament\Resources\JawaraIlkomerzResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJawaraIlkomerzs extends ListRecords
{
    protected static string $resource = JawaraIlkomerzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
