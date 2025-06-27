<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Community;
use Filament\Tables\Table;
use App\Models\JawaraIlkomerz;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JawaraIlkomerzResource\Pages;
use App\Filament\Resources\JawaraIlkomerzResource\RelationManagers;

class JawaraIlkomerzResource extends Resource
{
    protected static ?string $model = JawaraIlkomerz::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()?->email, [
            config('admin.admin_email'),
            config('admin.admin_education_email')
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make("image")
                ->image()
                ->label("Gambar / Poster Lomba")
                ->disk("public")
                ->maxSize(2048)
                ->nullable()
                ->columnSpanFull(),

            TextInput::make('name')
                ->label('Nama Lomba')
                ->placeholder('Ex. Lomba Koding Nasional 2025')
                ->required(),

            TextInput::make('organizer')
                ->label('Penyelenggara Lomba')
                ->placeholder('Ex. HIMALKOM Harvard')
                ->required(),

            TextInput::make('link')
                ->label('Link Pendaftaran / Guidebook')
                ->placeholder('https://example.com/pendaftaran')
                ->url(),

            Grid::make(2)->schema([
                Select::make('community_id')
                    ->label('Komunitas')
                    ->options(Community::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
            ]),

            DatePicker::make('start_date')
                ->label('Tanggal Mulai')
                ->placeholder('Tanggal mulai lomba / timeline')
                ->required(),

            DatePicker::make('end_date')
                ->label('Tanggal Selesai')
                ->placeholder('Tanggal selesai lomba / timeline')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Poster')
                    ->disk('public')
                    ->circular()
                    ->height(60),

                TextColumn::make('name')
                    ->label('Nama Lomba')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('organizer')
                    ->label('Penyelenggara')
                    ->searchable()
                    ->sortable(),

                TextColumn::make("link")
                    ->url(fn(JawaraIlkomerz $ji) => $ji->link)
                    ->wrap()
                    ->color("primary")
                    ->openUrlInNewTab(),

                TextColumn::make('community.name')
                    ->label('Komunitas')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ?? 'Miscellaneous'),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date()
                    ->sortable(),

                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('last updated')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('community_id')
                    ->label('Filter by Community')
                    ->relationship('community', 'name')
                    ->preload()
                    ->searchable()
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
        return 3;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJawaraIlkomerzs::route('/'),
            'create' => Pages\CreateJawaraIlkomerz::route('/create'),
            'edit' => Pages\EditJawaraIlkomerz::route('/{record}/edit'),
        ];
    }
}
