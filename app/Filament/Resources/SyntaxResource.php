<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Syntax;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SyntaxResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SyntaxResource\RelationManagers;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class SyntaxResource extends Resource
{
    protected static ?string $model = Syntax::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->email, [
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
                            TextInput::make("title")->placeholder("Syntax Edisi IX")->required(),
                        ]),
                    Grid::make(2)
                        ->schema([
                            TextInput::make("year")->numeric()->placeholder("2024")->required()
                        ]),
                    TextInput::make("link")->placeholder("https://drive.google.com/file/.....")->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make("image"),
                TextColumn::make("title")->sortable(),
                TextColumn::make("year")->sortable(),
                TextColumn::make("link")->url(fn(Syntax $syntax) => $syntax->link)
                    ->color("primary")
                    ->openUrlInNewTab()
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('last updated')->since()->sortable(),
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
            'index' => Pages\ListSyntaxes::route('/'),
            'create' => Pages\CreateSyntax::route('/create'),
            'edit' => Pages\EditSyntax::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Syntax';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }
}
