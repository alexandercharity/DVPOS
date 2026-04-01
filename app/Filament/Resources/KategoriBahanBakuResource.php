<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriBahanBakuResource\Pages;
use App\Models\KategoriBahanBaku;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KategoriBahanBakuResource extends Resource
{
    protected static ?string $model = KategoriBahanBaku::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori Bahan Baku';
    protected static ?string $modelLabel = 'Kategori Bahan Baku';
    protected static ?string $pluralModelLabel = 'Kategori Bahan Baku';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()->isPemilik();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama')->required()->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nama')->searchable()->sortable(),
            TextColumn::make('bahanBaku_count')->label('Jumlah Bahan Baku')
                ->counts('bahanBaku'),
            TextColumn::make('created_at')->dateTime('d/m/Y')->label('Dibuat'),
        ])->defaultSort('nama');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriBahanBakus::route('/'),
            'create' => Pages\CreateKategoriBahanBaku::route('/create'),
            'edit' => Pages\EditKategoriBahanBaku::route('/{record}/edit'),
        ];
    }
}
