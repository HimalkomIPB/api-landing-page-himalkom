<?php

namespace App\Filament\Resources\KomnewsCategoryResource\Pages;

use App\Filament\Resources\KomnewsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKomnewsCategory extends EditRecord
{
    protected static string $resource = KomnewsCategoryResource::class;

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
