<?php

namespace App\Filament\Resources\CommunityPortofolioResource\Pages;

use App\Filament\Resources\CommunityPortofolioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCommunityPortofolio extends CreateRecord
{
    protected static string $resource = CommunityPortofolioResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
