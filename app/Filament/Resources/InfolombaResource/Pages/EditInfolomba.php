<?php

namespace App\Filament\Resources\InfolombaResource\Pages;

use App\Filament\Resources\InfolombaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfolomba extends EditRecord
{
    protected static string $resource = InfolombaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
