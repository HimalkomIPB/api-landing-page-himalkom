<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityPortofolioResource\Pages;
use App\Models\Community;
use App\Models\CommunityPortofolio;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CommunityPortofolioResource extends Resource
{
    protected static ?string $model = CommunityPortofolio::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()?->email, [
            config('admin.admin_email'),
            config('admin.admin_education_email'),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->placeholder('Ex. Website Gacor')->required()->columns(1),
                FileUpload::make('image')
                    ->image()
                    ->label('Gambar')
                    ->disk('public')
                    ->maxSize(2048)
                    ->required()->columnSpanFull(),
                TextInput::make('author')->placeholder('Ex. Budi James')->required()->columns(1),
                Textarea::make('description')->placeholder('Deskripsi portofolio komunitas')->required()->columnSpanFull(),
                Grid::make(2)->schema([
                    Select::make('community_id')
                        ->label('Komunitas')
                        ->options(Community::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),
                TextInput::make('link')->label('Link projek')->placeholder('https://github.com/username/.....')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name')
                    ->sortable()
                    ->wrap(),
                TextColumn::make('community.name')->label('Komunitas')->sortable(),
                TextColumn::make('link')->url(fn (CommunityPortofolio $model) => $model->link)->color('primary')->openUrlInNewTab(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('last updated')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('community_id')
                    ->label('Filter by Community')
                    ->relationship('community', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\EditAction::make(),
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
        return 2;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommunityPortofolios::route('/'),
            'create' => Pages\CreateCommunityPortofolio::route('/create'),
            'edit' => Pages\EditCommunityPortofolio::route('/{record}/edit'),
        ];
    }
}
