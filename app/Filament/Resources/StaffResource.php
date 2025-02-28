<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Staff;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StaffResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StaffResource\RelationManagers;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-c-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make("name")->columns(1)->required(),
                Checkbox::make("isKetua")->label("Ketua Departemen?")->columnSpanFull(),
                TextInput::make("jabatan")->placeholder("Ex: Anggota, Ketua, Sekretaris")->required(),
                Select::make('division_id')
                    ->label('Divisi')
                    ->options(Division::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                FileUpload::make("image")
                    ->image()
                    ->label("Foto anggota")
                    ->disk("public")
                    ->maxSize(2048)
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make("image")->label("Foto anggota")->circular(),
                TextColumn::make("name")->searchable()->sortable(),
                TextColumn::make("division.name")->label("Divisi")->searchable()->sortable(),
                IconColumn::make("isKetua")->label("Ketua?")->boolean(),
                TextColumn::make("jabatan"),
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Divisions and Staff';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
