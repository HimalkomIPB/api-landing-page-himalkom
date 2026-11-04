<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfolombaCategoryResource\Pages;
use App\Models\InfolombaCategory;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InfolombaCategoryResource extends Resource
{
    protected static ?string $model = InfolombaCategory::class;

    protected static ?string $slug = 'infolomba-categories';

    protected static ?string $modelLabel = 'Kategori Lomba';

    protected static ?string $pluralModelLabel = 'Kategori Lomba';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Info Lomba';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return filled(config('admin.admin_email')) && Auth::user()?->email === config('admin.admin_email');
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny() && ! $record->infolombas()->exists();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->label('Nama Kategori')
                    ->placeholder('Contoh: Programming, Desain, Karya Tulis')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Kategori yang masih digunakan tidak dapat dihapus. Pindahkan info lombanya terlebih dahulu.'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Kategori')->searchable()->sortable(),
                TextColumn::make('infolombas_count')->label('Jumlah Lomba')->counts('infolombas')->sortable(),
            ])
            ->defaultSort('name')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfolombaCategories::route('/'),
            'create' => Pages\CreateInfolombaCategory::route('/create'),
            'edit' => Pages\EditInfolombaCategory::route('/{record}/edit'),
        ];
    }
}
