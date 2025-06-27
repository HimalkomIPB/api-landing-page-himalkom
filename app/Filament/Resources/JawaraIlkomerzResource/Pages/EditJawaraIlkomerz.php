<?php

namespace App\Filament\Resources\JawaraIlkomerzResource\Pages;

use App\Filament\Resources\JawaraIlkomerzResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJawaraIlkomerz extends EditRecord
{
    protected static string $resource = JawaraIlkomerzResource::class;

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
