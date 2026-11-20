<?php

namespace App\Filament\Resources\InfoMagangResource\Pages;

use App\Filament\Resources\InfoMagangResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInfoMagang extends CreateRecord
{
    protected static string $resource = InfoMagangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
