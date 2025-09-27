<?php

namespace App\Filament\Resources\MegaprokerResource\Pages;

use App\Filament\Resources\MegaprokerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMegaproker extends CreateRecord
{
    protected static string $resource = MegaprokerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
