<?php

namespace App\Filament\Resources\InfoMagangResource\Pages;

use App\Filament\Resources\InfoMagangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInfoMagang extends EditRecord
{
    protected static string $resource = InfoMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
