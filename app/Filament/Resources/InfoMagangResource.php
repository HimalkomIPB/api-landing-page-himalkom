<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfoMagangResource\Pages;
use App\Models\InfoMagang;

class InfoMagangResource extends RegistrationInfoResource
{
    protected static ?string $model = InfoMagang::class;

    protected static ?string $slug = 'info-magang';

    protected static ?string $modelLabel = 'Info Magang';

    protected static ?string $pluralModelLabel = 'Info Magang';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfoMagang::route('/'),
            'create' => Pages\CreateInfoMagang::route('/create'),
            'edit' => Pages\EditInfoMagang::route('/{record}/edit'),
        ];
    }
}
