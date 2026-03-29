<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return true; // kasir & pemilik bisa lihat
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isPemilik();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->isPemilik();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->isPemilik();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('kategori_id')
                ->relationship('kategori', 'nama')
                ->required()
                ->label('Kategori'),
            TextInput::make('nama')->required()->maxLength(255),
            TextInput::make('harga')->numeric()->prefix('Rp')->required(),
            TextInput::make('stok')->numeric()->default(0)->required(),
            Toggle::make('tersedia')->label('Tersedia')->default(true)->inline(false),
            Textarea::make('deskripsi')->rows(3),
            FileUpload::make('gambar')->image()->directory('produk')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            ImageColumn::make('gambar')->circular(),
            TextColumn::make('nama')->searchable()->sortable(),
            TextColumn::make('kategori.nama')->label('Kategori')->sortable(),
            TextColumn::make('harga')->money('IDR')->sortable(),
            TextColumn::make('stok')->sortable()
                ->color(fn ($state) => $state <= 5 ? 'danger' : 'success'),
            IconColumn::make('tersedia')->boolean()->label('Tersedia'),
        ])->defaultSort('id', 'desc')
        ->actions([
            Action::make('toggleTersedia')
                ->label(fn ($record) => $record->tersedia ? 'Set Habis' : 'Set Tersedia')
                ->icon(fn ($record) => $record->tersedia ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn ($record) => $record->tersedia ? 'danger' : 'success')
                ->visible(fn () => true)
                ->action(fn ($record) => $record->update(['tersedia' => !$record->tersedia])),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
