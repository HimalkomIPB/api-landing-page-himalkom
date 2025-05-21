<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Komnews;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KomnewsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KomnewsResource\RelationManagers;

class KomnewsResource extends Resource
{
    protected static ?string $model = Komnews::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

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
                    FileUpload::make("image")
                        ->image()
                        ->label("Image Cover")
                        ->disk("public")
                        ->maxSize(2048)
                        ->required(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make("title")
                                ->required()
                        ]),
                    Grid::make(3)
                        ->schema([
                            Select::make("categories")
                                ->multiple()
                                ->relationship(titleAttribute: "name")
                                ->searchable()
                                ->preload()
                                ->required()
                        ]),
                    RichEditor::make('content')
                        ->label("Content")
                        ->placeholder("Lorem ipsum dolor sit amet....")
                        ->disableToolbarButtons(["code"])
                        ->required()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('title')
                    ->description(fn(Komnews $news): string => $news->excerp)
                    ->wrap(),
                TextColumn::make('slug')->wrap(),
                TextColumn::make('categories.name')
                    ->label('Categories'),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('last updated')->since()->sortable(),
            ])
            ->filters([
                //
            ])
            ->persistFiltersInSession() 
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
            'index' => Pages\ListKomnews::route('/'),
            'create' => Pages\CreateKomnews::route('/create'),
            'edit' => Pages\EditKomnews::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Komnews';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
