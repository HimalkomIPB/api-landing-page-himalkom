<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IGalleryResource\Pages;
use App\Models\IGallery;
use App\Models\IGallerySubject;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
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

class IGalleryResource extends Resource
{
    protected static ?string $model = IGallery::class;

    protected static ?string $navigationIcon = 'heroicon-s-photo';

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
                    FileUpload::make('image')
                        ->image()
                        ->label('Image')
                        ->disk('public')
                        ->maxSize(2048)
                        ->required(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')->placeholder('Judul proyek')->required(),
                        ]),
                    Grid::make(2)
                        ->schema([
                            TextArea::make('description')->placeholder('deskripsi singkat proyek')->required(),
                        ]),
                    Grid::make(2)->schema([
                        Select::make('subject_id')
                            ->label('Subject')
                            ->options(IGallerySubject::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('contributor')->placeholder('Format: Kirito, Asuna, Shion KOM 60')->required(),
                            TextInput::make('angkatan')->numeric()->placeholder('Ex: 59')->required(),
                        ]),
                    TextInput::make('link')->label('Link projek')->placeholder('https://github.com/username/.....')->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name')
                    ->sortable()
                    ->description(fn (IGallery $iGallery): string => $iGallery->description)
                    ->wrap(),
                TextColumn::make('contributor')
                    ->description(fn (IGallery $iGallery): string => 'KOM '.$iGallery->angkatan),
                TextColumn::make('subject.name')->label('subject')->sortable(),
                TextColumn::make('link')->url(fn (IGallery $iGallery) => $iGallery->link)->color('primary')->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('subject_id')
                    ->label('Filter by Subject')
                    ->relationship('subject', 'name')
                    ->preload()
                    ->searchable(),
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
            'index' => Pages\ListIGalleries::route('/'),
            'create' => Pages\CreateIGallery::route('/create'),
            'edit' => Pages\EditIGallery::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'IGallery';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
