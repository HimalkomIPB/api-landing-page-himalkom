<?php

namespace App\Filament\Resources\InfoBeasiswaResource\Pages;

use App\Filament\Resources\InfoBeasiswaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInfoBeasiswa extends EditRecord
{
    protected static string $resource = InfoBeasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
