<?php

namespace App\Filament\Resources\InfolombaCategoryResource\Pages;

use App\Filament\Resources\InfolombaCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInfolombaCategory extends CreateRecord
{
    protected static string $resource = InfolombaCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
