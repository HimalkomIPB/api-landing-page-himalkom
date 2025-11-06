<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrestasiResource\Pages;
use App\Filament\Resources\PrestasiResource\Pages\ListPrestasis;
use App\Models\Prestasi;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\ImageColumn;


class PrestasiResource extends Resource
{
    protected static ?string $model = Prestasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Prestasi';
    protected static ?int $navigationSort = 2;
    public static function canViewAny(): bool
    {
        return in_array(Auth::user()?->email, [
            config('admin.admin_email'),
            config('admin.admin_education_email'),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama')->required()->placeholder('Ex. Juara 1 Hackathon Ilkomers')->columns(1),
            Grid::make(2)->schema([
                TextInput::make('tahun')
                    ->label('Tahun (YYYY)')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue((int) date('Y'))
                    ->placeholder('2024')
                    ->required(),
                TextInput::make('penyelenggara')->placeholder('Ex. Himpunan Ilkom')->required(),
            ])->columnSpanFull(),
            RichEditor::make('deskripsi')
                ->label('Deskripsi Kegiatan')
                ->placeholder('Lorem ipsum dolor sit amet....')
                ->disableToolbarButtons(['code'])
                ->required()
                ->columnSpanFull(),
            Grid::make(2)->schema([
                TextInput::make('lokasi')->placeholder('Kota atau online')->columns(1),
                Select::make('prestasi_kategori_id')
                    ->label('Kategori')
                    ->relationship('prestasiKategori', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Kategori Prestasi')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nasional/Internasional'),

                    ])
                    ->helperText('Bisa pilih kategori yang sudah ada atau klik "+" untuk menambah baru.'),

            ])->columnSpanFull(),
            FileUpload::make('bukti_path')
                ->label('Dokumentasi Kegiatan')
                ->image()
                ->disk('public')
                ->directory('prestasi')
                ->maxSize(2048)
                ->preserveFilenames(false)
                ->required()
                ->columnSpanFull(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable()->label('ID'),
            TextColumn::make('nama')->searchable()->wrap(),
            TextColumn::make('tahun')->sortable(),
            TextColumn::make('penyelenggara')->limit(30),
            TextColumn::make('prestasiKategori.name')->label('Kategori')->sortable()->limit(30),
            TextColumn::make('created_at')->dateTime()->sortable(),
            TextColumn::make('updated_at')->since()->sortable()->label('Last updated'),
            ImageColumn::make('bukti_path')
                ->label('Dokumentasi Kegiatan')
                ->disk('public')
                ->square()
                ->height(60)
                ->extraImgAttributes(['class' => 'object-cover rounded-md'])
                ->toggleable(false),

        ])
            ->filters([
                SelectFilter::make('tahun')
                    ->label('Filter Tahun')
                    ->options(fn() => Prestasi::query()->pluck('tahun', 'tahun')->unique()->sortDesc()->toArray())
                    ->placeholder('Semua tahun'),
                SelectFilter::make('prestasi_kategori_id')
                    ->label('Kategori')
                    ->relationship('prestasiKategori', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua kategori'),
            ])
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Prestasi Ilkomers';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrestasis::route('/'),
            'create' => Pages\CreatePrestasi::route('/create'),
            'edit' => Pages\EditPrestasi::route('/{record}/edit'),
        ];
    }
}
