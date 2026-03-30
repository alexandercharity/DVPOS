<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianResource\Pages;
use App\Models\BahanBaku;
use App\Models\Pembelian;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PembelianResource extends Resource
{
    protected static ?string $model = Pembelian::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pembelian Bahan Baku';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->isPemilik();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('supplier_id')
                ->relationship('supplier', 'nama')
                ->required()
                ->label('Supplier'),
            DatePicker::make('tanggal')->required()->default(now()),
            Textarea::make('keterangan')->rows(2)->nullable(),
            Repeater::make('detailPembelian')
                ->relationship()
                ->schema([
                    Select::make('bahan_baku_id')
                        ->label('Bahan Baku')
                        ->options(BahanBaku::all()->pluck('nama', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            // reset harga saat ganti bahan baku
                            $set('harga_beli', null);
                            $set('subtotal', null);
                        }),
                    TextInput::make('jumlah')->numeric()->required()->default(1)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $set('subtotal', floatval($get('jumlah')) * floatval($get('harga_beli')));
                        }),
                    TextInput::make('harga_beli')->numeric()->prefix('Rp')->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $set('subtotal', floatval($get('jumlah')) * floatval($get('harga_beli')));
                        }),
                    TextInput::make('subtotal')->numeric()->prefix('Rp')->readOnly(),
                ])->columns(4)
                ->label('Detail Pembelian')
                ->addActionLabel('Tambah Bahan Baku'),
            Placeholder::make('total_label')
                ->label('Total Pembelian')
                ->content(function (Get $get) {
                    $items = $get('detailPembelian') ?? [];
                    $total = collect($items)->sum(fn($i) => floatval($i['subtotal'] ?? 0));
                    return 'Rp ' . number_format($total, 0, ',', '.');
                })->live()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('supplier.nama')->label('Supplier')->searchable(),
            TextColumn::make('tanggal')->date('d/m/Y')->sortable(),
            TextColumn::make('total')->money('IDR'),
            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn ($state) => $state === 'lunas' ? 'success' : 'warning')
                ->formatStateUsing(fn ($state) => $state === 'lunas' ? 'Lunas' : 'Belum Lunas'),
            TextColumn::make('user.name')->label('Dicatat Oleh'),
            TextColumn::make('created_at')->dateTime('d/m/Y H:i')->label('Waktu'),
        ])
        ->defaultSort('id', 'desc')
        ->actions([
            Action::make('konfirmasi_lunas')
                ->label('Konfirmasi Lunas')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'belum_lunas')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pembayaran ke Supplier')
                ->modalDescription('Tandai pembelian ini sudah dibayar lunas ke supplier?')
                ->action(fn ($record) => $record->update(['status' => 'lunas'])),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelians::route('/'),
            'create' => Pages\CreatePembelian::route('/create'),
            'edit' => Pages\EditPembelian::route('/{record}/edit'),
        ];
    }
}
