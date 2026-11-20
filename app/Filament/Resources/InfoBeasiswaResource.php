<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfoBeasiswaResource\Pages;
use App\Models\InfoBeasiswa;

class InfoBeasiswaResource extends RegistrationInfoResource
{
    protected static ?string $model = InfoBeasiswa::class;

    protected static ?string $slug = 'info-beasiswa';

    protected static ?string $modelLabel = 'Info Beasiswa';

    protected static ?string $pluralModelLabel = 'Info Beasiswa';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfoBeasiswa::route('/'),
            'create' => Pages\CreateInfoBeasiswa::route('/create'),
            'edit' => Pages\EditInfoBeasiswa::route('/{record}/edit'),
        ];
    }
}
