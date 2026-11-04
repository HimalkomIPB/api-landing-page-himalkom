<?php

namespace App\Filament\Resources\InfolombaCategoryResource\Pages;

use App\Filament\Resources\InfolombaCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfolombaCategory extends EditRecord
{
    protected static string $resource = InfolombaCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
