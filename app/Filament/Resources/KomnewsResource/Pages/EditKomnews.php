<?php

namespace App\Filament\Resources\KomnewsResource\Pages;

use App\Filament\Resources\KomnewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKomnews extends EditRecord
{
    protected static string $resource = KomnewsResource::class;

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
