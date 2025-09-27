<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionResource\Pages;
use App\Models\Division;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

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
                FileUpload::make('logo')
                    ->image()
                    ->label('Logo Divisi')
                    ->disk('public')
                    ->maxSize(2048)
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('name')->placeholder('nama lengkap divisi')->required(),
                TextInput::make('abbreviation')->placeholder('singkatan divisi, ex: BPH, ACE')->required(),
                Textarea::make('description')->placeholder('deskripsi divisi')->required(),
                Repeater::make('workPrograms')
                    ->label('Program Kerja')
                    ->relationship('workPrograms')
                    ->schema([
                        TextInput::make('name')->placeholder('Ex. Upgrading Himalkom'),
                        Textarea::make('description')->placeholder('Ex. Proker ini adalah.....'),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')->circular(),
                TextColumn::make('name'),
                TextColumn::make('abbreviation'),
                TextColumn::make('slug')->label('slug (auto generated)'),
                TextColumn::make('description')->limit(100)->wrap(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('last updated')->since()->sortable(),
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
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Divisions and Staff';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
