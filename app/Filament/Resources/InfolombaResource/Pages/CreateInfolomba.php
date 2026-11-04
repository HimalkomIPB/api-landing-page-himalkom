<?php

namespace App\Filament\Resources\InfolombaResource\Pages;

use App\Filament\Resources\InfolombaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInfolomba extends CreateRecord
{
    protected static string $resource = InfolombaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
