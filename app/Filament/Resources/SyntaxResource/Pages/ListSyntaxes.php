<?php

namespace App\Filament\Resources\SyntaxResource\Pages;

use App\Filament\Resources\SyntaxResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSyntaxes extends ListRecords
{
    protected static string $resource = SyntaxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
