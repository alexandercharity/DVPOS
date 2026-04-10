<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResepResource\Pages;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\Resep;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ResepResource extends Resource
{
    protected static ?string $model = Resep::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Resep Produk';
    protected static ?string $modelLabel = 'Resep';
    protected static ?string $pluralModelLabel = 'Resep Produk';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return auth()->user()->isPemilik();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('produk_id')
                ->label('Produk')
                ->options(Produk::all()->pluck('nama', 'id'))
                ->required()
                ->searchable(),
            Select::make('bahan_baku_id')
                ->label('Bahan Baku')
                ->options(BahanBaku::all()->pluck('nama', 'id'))
                ->required()
                ->searchable()
                ->live()
                ->afterStateUpdated(fn ($state, $set) =>
                    $set('satuan', BahanBaku::find($state)?->satuan ?? 'gram')
                ),
            TextInput::make('jumlah')
                ->label('Jumlah per Porsi')
                ->numeric()
                ->inputMode('decimal')
                ->step(0.001)
                ->required()
                ->minValue(0.001),
            Select::make('satuan')
                ->label('Satuan')
                ->options([
                    'gram' => 'Gram',
                    'ml' => 'Ml',
                    'kg' => 'Kg',
                    'liter' => 'Liter',
                    'pcs' => 'Pcs',
                    'ikat' => 'Ikat',
                    'butir' => 'Butir',
                    'bungkus' => 'Bungkus',
                ])
                ->required()
                ->default('gram'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('produk.nama')->label('Produk')->sortable()->searchable(),
            TextColumn::make('bahanBaku.nama')->label('Bahan Baku')->searchable(),
            TextColumn::make('jumlah')->label('Jumlah/Porsi')
                ->formatStateUsing(fn ($state, $record) => ($state + 0) . ' ' . $record->satuan),
        ])
        ->defaultGroup('produk.nama')
        ->groups([
            Group::make('produk.nama')->label('Produk')->collapsible(),
        ])
        ->defaultSort('produk_id')
        ->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReseps::route('/'),
            'create' => Pages\CreateResep::route('/create'),
            'edit' => Pages\EditResep::route('/{record}/edit'),
        ];
    }
}
