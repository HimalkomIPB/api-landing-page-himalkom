<?php

namespace App\Filament\Resources\MegaprokerResource\Pages;

use App\Filament\Resources\MegaprokerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMegaproker extends EditRecord
{
    protected static string $resource = MegaprokerResource::class;

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
