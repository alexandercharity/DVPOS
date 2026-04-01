<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Supplier';
    protected static ?string $modelLabel = 'Supplier';
    protected static ?string $pluralModelLabel = 'Supplier';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->isPemilik();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama')->required()->maxLength(255),
            TextInput::make('telepon')->tel()->maxLength(20),
            TextInput::make('email')->email()->maxLength(255),
            Textarea::make('alamat')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('nama')->searchable()->sortable(),
            TextColumn::make('telepon')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('alamat')->limit(40),
        ])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
