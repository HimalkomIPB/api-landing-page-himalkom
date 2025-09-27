<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MegaprokerResource\Pages;
use App\Models\Megaproker;
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

class MegaprokerResource extends Resource
{
    protected static ?string $model = Megaproker::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                TextInput::make('name')->placeholder('Ex. IT TODAY')->required()->columns(1),
                FileUpload::make('logo')
                    ->image()
                    ->label('Logo')
                    ->disk('public')
                    ->maxSize(2048)
                    ->required()->columnSpanFull(),
                Textarea::make('description')->placeholder('Deskripsi mega proker')->required()->columnSpanFull(),
                TextInput::make('video_url')->placeholder('Ex. https://youtube.com/....')->required(),
                Repeater::make('images')
                    ->relationship('images')
                    ->schema([
                        FileUpload::make('url')
                            ->label('images')
                            ->image()
                            ->disk('public')
                            ->maxSize(2048)
                            ->required(),
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
                    ->description(fn (Megaproker $mp): string => $mp->description)->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('video_url')->url(fn (Megaproker $mp) => $mp->video_url)
                    ->color('primary')
                    ->openUrlInNewTab()
                    ->wrap(),
                ImageColumn::make('images.url')->circular(),
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

    public static function getNavigationGroup(): ?string
    {
        return 'Megaproker';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMegaprokers::route('/'),
            'create' => Pages\CreateMegaproker::route('/create'),
            'edit' => Pages\EditMegaproker::route('/{record}/edit'),
        ];
    }
}
