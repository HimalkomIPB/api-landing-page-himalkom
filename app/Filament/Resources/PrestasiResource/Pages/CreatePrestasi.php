<?php

namespace App\Filament\Resources\PrestasiResource\Pages;

use App\Filament\Resources\PrestasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrestasi extends CreateRecord
{
    protected static string $resource = PrestasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
