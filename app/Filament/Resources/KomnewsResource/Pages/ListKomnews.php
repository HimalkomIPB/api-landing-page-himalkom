<?php

namespace App\Filament\Resources\KomnewsResource\Pages;

use App\Filament\Resources\KomnewsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKomnews extends ListRecords
{
    protected static string $resource = KomnewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
