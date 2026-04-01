<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriResource\Pages;
use App\Models\Kategori;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori';
    protected static ?string $modelLabel = 'Kategori';
    protected static ?string $pluralModelLabel = 'Kategori';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

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
            TextColumn::make('id')->sortable(),
            TextColumn::make('nama')->searchable()->sortable(),
            TextColumn::make('created_at')->dateTime('d/m/Y')->label('Dibuat'),
        ])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoris::route('/'),
            'create' => Pages\CreateKategori::route('/create'),
            'edit' => Pages\EditKategori::route('/{record}/edit'),
        ];
    }
}
