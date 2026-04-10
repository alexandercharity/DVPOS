<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BahanBakuResource\Pages;
use App\Models\BahanBaku;
use App\Models\KategoriBahanBaku;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BahanBakuResource extends Resource
{
    protected static ?string $model = BahanBaku::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Bahan Baku';
    protected static ?string $modelLabel = 'Bahan Baku';
    protected static ?string $pluralModelLabel = 'Bahan Baku';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()->isPemilik();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('kategori_bahan_baku_id')
                ->label('Kategori')
                ->options(KategoriBahanBaku::all()->pluck('nama', 'id'))
                ->searchable()
                ->nullable()
                ->createOptionForm([
                    TextInput::make('nama')->required()->maxLength(255),
                ])
                ->createOptionUsing(fn (array $data) => KategoriBahanBaku::create($data)->id),
            TextInput::make('nama')->required()->maxLength(255),
            Select::make('satuan')
                ->options([
                    'gram' => 'Gram',
                    'ml' => 'Ml',
                    'kg' => 'Kg',
                    'liter' => 'Liter',
                    'pcs' => 'Pcs',
                    'ikat' => 'Ikat',
                    'butir' => 'Butir',
                    'bungkus' => 'Bungkus',
                ])->required()->default('gram'),
            TextInput::make('stok')
                ->numeric()
                ->default(0)
                ->readOnly()
                ->step(0.001)
                ->inputMode('decimal')
                ->formatStateUsing(fn ($state) => rtrim(rtrim(number_format((float)$state, 3, '.', ''), '0'), '.'))
                ->label('Stok')
                ->helperText('Stok otomatis bertambah saat ada pembelian bahan baku'),
            TextInput::make('stok_minimum')->numeric()->default(10)->required()
                ->step(0.001)
                ->inputMode('decimal')
                ->formatStateUsing(fn ($state) => rtrim(rtrim(number_format((float)$state, 3, '.', ''), '0'), '.'))
                ->label('Stok Minimum (Notif)')
                ->helperText('Notif muncul kalau stok di bawah angka ini'),
            Textarea::make('keterangan')->rows(2)->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('kategoriBahanBaku.nama')->label('Kategori')->sortable()->badge()->color('info'),
            TextColumn::make('nama')->searchable()->sortable(),
            TextColumn::make('satuan'),
            TextColumn::make('stok')->sortable()
                ->formatStateUsing(fn ($state, $record) => rtrim(rtrim(number_format((float)$state, 3, '.', ''), '0'), '.') . ' ' . $record->satuan)
                ->color(fn ($state, $record) => $state <= $record->stok_minimum ? 'danger' : 'success'),
            TextColumn::make('stok_minimum')->label('Min. Stok')
                ->formatStateUsing(fn ($state, $record) => rtrim(rtrim(number_format((float)$state, 3, '.', ''), '0'), '.') . ' ' . $record->satuan),
            TextColumn::make('keterangan')->limit(40),
        ])
        ->defaultSort('kategori_bahan_baku_id')
        ->filters([
            SelectFilter::make('kategori_bahan_baku_id')
                ->label('Kategori')
                ->options(KategoriBahanBaku::all()->pluck('nama', 'id')),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBahanBakus::route('/'),
            'create' => Pages\CreateBahanBaku::route('/create'),
            'edit' => Pages\EditBahanBaku::route('/{record}/edit'),
        ];
    }
}
