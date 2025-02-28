<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KomnewsCategory;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KomnewsCategoryResource\Pages;
use App\Filament\Resources\KomnewsCategoryResource\RelationManagers;
use App\Filament\Resources\KomnewsCategoryResource\Pages\ListKomnewsCategories;
use App\Filament\Resources\KomnewsCategoryResource\Pages\CreateKomnewsCategory;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class KomnewsCategoryResource extends Resource
{
    protected static ?string $model = KomnewsCategory::class;
    public static ?string $label = "Category";

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label("Name")
                        ->placeholder("ex: study, collaboration")
                        ->required()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
