<?php

namespace App\Filament\Resources\KomnewsResource\Pages;

use App\Filament\Resources\KomnewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKomnews extends CreateRecord
{
    protected static string $resource = KomnewsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
