<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Community;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CommunityResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CommunityResource\RelationManagers;

class CommunityResource extends Resource
{
    protected static ?string $model = Community::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->placeholder('Ex. AgriUX')->required()->columns(1),
                FileUpload::make("logo")
                    ->image()
                    ->label("Logo")
                    ->disk("public")
                    ->maxSize(2048)
                    ->required()->columnSpanFull(),
                Textarea::make('description')->placeholder("Deskripsi komunitas")->required()->columnSpanFull(),
                TextInput::make('instagram')->placeholder('Ex. agriux')->required()->columns(1),
                TextInput::make('contact')->placeholder('Ex. John Doe')->required()->columns(1),
                TextInput::make('contact_whatsapp')->placeholder('Ex. 0812345678')->required()->columns(1),
                Repeater::make('purposes')
                    ->schema([
                        TextInput::make('value')->label('purpose'),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                Repeater::make('achievements')
                    ->schema([
                        TextInput::make('value')->label('achievement'),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                Repeater::make('images')
                    ->label('Documentations')
                    ->relationship('images')
                    ->schema([
                        FileUpload::make('url')
                            ->label('image')
                            ->image()
                            ->disk("public")
                            ->maxSize(2048)
                            ->required()
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
                TextColumn::make('name')
                    ->description(fn(Community $comm): string => Str::limit($comm->description, 100, '...'))
                    ->wrap(),
                TextColumn::make('slug'),
                TextColumn::make('instagram')->url(fn(Community $comm) => 'https://instagram.com/' . $comm->instagram)
                    ->color("primary")
                    ->openUrlInNewTab(),
                TextColumn::make('contact')
                    ->description(fn(Community $comm): string => $comm->contact_whatsapp)
                    ->wrap()
                    ->limit(50),
                ImageColumn::make('images.url')->square()->wrap(),
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

    public static function getNavigationGroup(): ?string
    {
        return 'Komunitas Ilkom';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommunities::route('/'),
            'create' => Pages\CreateCommunity::route('/create'),
            'edit' => Pages\EditCommunity::route('/{record}/edit'),
        ];
    }
}
