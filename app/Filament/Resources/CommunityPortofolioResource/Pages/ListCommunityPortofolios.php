<?php

namespace App\Filament\Resources\CommunityPortofolioResource\Pages;

use App\Filament\Resources\CommunityPortofolioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommunityPortofolios extends ListRecords
{
    protected static string $resource = CommunityPortofolioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
