<?php

namespace App\Filament\Resources\PrestasiKategoriResource\Pages;

use App\Filament\Resources\PrestasiKategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrestasiKategoris extends ListRecords
{
    protected static string $resource = PrestasiKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
