<?php

namespace App\Filament\Resources\SyntaxResource\Pages;

use App\Filament\Resources\SyntaxResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSyntax extends CreateRecord
{
    protected static string $resource = SyntaxResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
