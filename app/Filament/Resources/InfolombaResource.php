<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfolombaResource\Pages;
use App\Models\Infolomba;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InfolombaResource extends Resource
{
    protected static ?string $model = Infolomba::class;

    protected static ?string $slug = 'infolomba';

    protected static ?string $modelLabel = 'Info Lomba';

    protected static ?string $pluralModelLabel = 'Info Lomba';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Info Lomba';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return filled(config('admin.admin_email')) && Auth::user()?->email === config('admin.admin_email');
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Lomba')->schema([
                FileUpload::make('image')
                    ->label('Image Cover')
                    ->image()
                    ->disk('public')
                    ->directory('infolomba')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->helperText('Maksimal 2 MB.')
                    ->required(),
                TextInput::make('title')
                    ->label('Title')
                    ->maxLength(255)
                    ->required(),
                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                    ->required(),
                Select::make('infolomba_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name', fn (Builder $query) => $query->orderBy('name'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->unique('infolomba_categories', 'name'),
                    ])
                    ->helperText('Pilih kategori atau klik "+" untuk membuat kategori baru.'),
                Grid::make(2)->schema([
                    DatePicker::make('start_date')
                        ->label('Mulai Pendaftaran')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required(),
                    DatePicker::make('end_date')
                        ->label('Akhir Pendaftaran')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->afterOrEqual('start_date')
                        ->validationMessages(['after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.'])
                        ->required(),
                ]),
                TextInput::make('link')
                    ->label('Link')
                    ->placeholder('https://...')
                    ->url()
                    ->rules(['url:http,https'])
                    ->maxLength(2048)
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Cover')->disk('public')->square(),
                TextColumn::make('title')->label('Title')->searchable()->sortable()->wrap(),
                TextColumn::make('category.name')->label('Kategori')->sortable(),
                TextColumn::make('start_date')->label('Mulai')->date('d/m/Y')->sortable(),
                TextColumn::make('end_date')->label('Selesai')->date('d/m/Y')->sortable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'upcoming' => 'Akan Datang',
                        'active' => 'Dibuka',
                        'closed' => 'Ditutup',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'upcoming' => 'info',
                        'active' => 'success',
                        'closed' => 'gray',
                    }),
                TextColumn::make('link')->label('Link')->limit(35)
                    ->url(fn (Infolomba $record) => $record->link)->openUrlInNewTab()->color('primary'),
                TextColumn::make('updated_at')->label('Terakhir Diubah')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('infolomba_category_id')->label('Kategori')
                    ->relationship('category', 'name')->searchable()->preload(),
                SelectFilter::make('status')->label('Status')
                    ->options(['upcoming' => 'Akan Datang', 'active' => 'Dibuka', 'closed' => 'Ditutup'])
                    ->query(function (Builder $query, array $data): Builder {
                        $today = today(config('app.timezone'))->toDateString();

                        return match ($data['value'] ?? null) {
                            'upcoming' => $query->whereDate('start_date', '>', $today),
                            'active' => $query->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today),
                            'closed' => $query->whereDate('end_date', '<', $today),
                            default => $query,
                        };
                    }),
            ])
            ->persistFiltersInSession()
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfolombas::route('/'),
            'create' => Pages\CreateInfolomba::route('/create'),
            'edit' => Pages\EditInfolomba::route('/{record}/edit'),
        ];
    }
}
