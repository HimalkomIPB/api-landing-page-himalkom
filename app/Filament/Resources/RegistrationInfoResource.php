<?php

namespace App\Filament\Resources;

use App\Models\RegistrationInfo;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class RegistrationInfoResource extends Resource
{
    protected static ?string $navigationGroup = 'Ilkomerz Hub';

    private const STATUS_LABELS = [
        'upcoming' => 'Akan Datang',
        'active' => 'Dibuka',
        'closed' => 'Ditutup',
    ];

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
            Section::make(static::getModelLabel())->schema([
                FileUpload::make('image')
                    ->label('Gambar Cover')
                    ->image()
                    ->disk('public')
                    ->directory(static::getSlug())
                    ->visibility('public')
                    ->maxSize(2048)
                    ->helperText('Maksimal 2 MB.')
                    ->required(),
                TextInput::make('title')
                    ->label('Judul')
                    ->maxLength(255)
                    ->required(),
                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                    ->required(),
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
                    ->label('Link Pendaftaran')
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
                TextColumn::make('title')->label('Judul')->searchable()->sortable()->wrap(),
                TextColumn::make('start_date')->label('Mulai')->date('d/m/Y')->sortable(),
                TextColumn::make('end_date')->label('Selesai')->date('d/m/Y')->sortable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUS_LABELS[$state])
                    ->color(fn (string $state) => match ($state) {
                        'upcoming' => 'info',
                        'active' => 'success',
                        'closed' => 'gray',
                    }),
                TextColumn::make('link')->label('Link')->limit(35)
                    ->url(fn (RegistrationInfo $record) => $record->link)->openUrlInNewTab()->color('primary'),
                TextColumn::make('updated_at')->label('Terakhir Diubah')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Status')
                    ->options(self::STATUS_LABELS)
                    ->query(fn (Builder $query, array $data): Builder => $query->withStatus($data['value'] ?? null)),
            ])
            ->persistFiltersInSession()
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }
}
