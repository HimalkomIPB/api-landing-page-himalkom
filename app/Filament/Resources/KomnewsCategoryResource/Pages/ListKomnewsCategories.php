<?php

namespace App\Filament\Resources\KomnewsCategoryResource\Pages;

use App\Filament\Resources\KomnewsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKomnewsCategories extends ListRecords
{
    protected static string $resource = KomnewsCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
