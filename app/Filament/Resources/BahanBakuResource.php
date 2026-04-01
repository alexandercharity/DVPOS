<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BahanBakuResource\Pages;
use App\Models\BahanBaku;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
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
            TextInput::make('nama')->required()->maxLength(255),
            Select::make('satuan')
                ->options([
                    'pcs' => 'Pcs',
                    'kg' => 'Kg',
                    'gram' => 'Gram',
                    'liter' => 'Liter',
                    'ml' => 'Ml',
                    'ikat' => 'Ikat',
                    'butir' => 'Butir',
                    'bungkus' => 'Bungkus',
                ])->required()->default('pcs'),
            TextInput::make('stok')->numeric()->default(0)->required(),
            Textarea::make('keterangan')->rows(2)->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('nama')->searchable()->sortable(),
            TextColumn::make('satuan'),
            TextColumn::make('stok')->sortable()
                ->color(fn ($state) => $state <= 5 ? 'danger' : 'success'),
            TextColumn::make('keterangan')->limit(40),
        ])->defaultSort('id', 'desc');
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
