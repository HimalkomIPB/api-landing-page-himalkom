<?php

namespace App\Filament\Resources\PrestasiKategoriResource\Pages;

use App\Filament\Resources\PrestasiKategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrestasiKategori extends EditRecord
{
    protected static string $resource = PrestasiKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
