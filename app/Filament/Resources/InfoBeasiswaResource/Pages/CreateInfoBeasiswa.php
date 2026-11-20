<?php

namespace App\Filament\Resources\InfoBeasiswaResource\Pages;

use App\Filament\Resources\InfoBeasiswaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInfoBeasiswa extends CreateRecord
{
    protected static string $resource = InfoBeasiswaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
