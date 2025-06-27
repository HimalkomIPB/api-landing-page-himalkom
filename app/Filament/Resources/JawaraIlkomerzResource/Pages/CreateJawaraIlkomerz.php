<?php

namespace App\Filament\Resources\JawaraIlkomerzResource\Pages;

use App\Filament\Resources\JawaraIlkomerzResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJawaraIlkomerz extends CreateRecord
{
    protected static string $resource = JawaraIlkomerzResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
