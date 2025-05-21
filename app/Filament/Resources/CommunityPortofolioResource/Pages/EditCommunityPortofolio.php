<?php

namespace App\Filament\Resources\CommunityPortofolioResource\Pages;

use App\Filament\Resources\CommunityPortofolioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommunityPortofolio extends EditRecord
{
    protected static string $resource = CommunityPortofolioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
