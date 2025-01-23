<?php

namespace App\Filament\Resources\KomnewsCategoryResource\Pages;

use App\Filament\Resources\KomnewsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKomnewsCategory extends CreateRecord
{
    protected static string $resource = KomnewsCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
