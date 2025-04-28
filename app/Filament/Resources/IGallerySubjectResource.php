<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\IGallerySubject;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\IGallerySubjectResource\Pages;
use App\Filament\Resources\IGallerySubjectResource\RelationManagers;
use Filament\Tables\Columns\TextColumn;

class IGallerySubjectResource extends Resource
{
    protected static ?string $model = IGallerySubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Grid::make(2)
                    ->schema([
                        TextInput::make("name")->placeholder("Ex: Basis Data, Analisis Sistem")->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")->sortable(),
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
            'index' => Pages\ListIGallerySubjects::route('/'),
            'create' => Pages\CreateIGallerySubject::route('/create'),
            'edit' => Pages\EditIGallerySubject::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'IGallery';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
