<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KomnewsCategoryResource\Pages;
use App\Models\KomnewsCategory;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class KomnewsCategoryResource extends Resource
{
    protected static ?string $model = KomnewsCategory::class;

    public static ?string $label = 'Category';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()?->email, [
            config('admin.admin_email'),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder('ex: study, collaboration')
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug'),
            ])
            ->filters([
                //
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKomnewsCategories::route('/'),
            'create' => Pages\CreateKomnewsCategory::route('/create'),
            'edit' => Pages\EditKomnewsCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Komnews';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
