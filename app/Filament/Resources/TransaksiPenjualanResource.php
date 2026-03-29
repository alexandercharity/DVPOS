<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiPenjualanResource\Pages;
use App\Models\Produk;
use App\Models\TransaksiPenjualan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransaksiPenjualanResource extends Resource
{
    protected static ?string $model = TransaksiPenjualan::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Transaksi Penjualan';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('kode_transaksi')
                ->default(fn () => 'TRX-' . strtoupper(uniqid()))
                ->readOnly()->required(),
            DatePicker::make('tanggal')->required()->default(now()),
            Repeater::make('detailPenjualan')
                ->relationship()
                ->schema([
                    Select::make('produk_id')
                        ->label('Produk')
                        ->options(Produk::where('stok', '>', 0)->where('tersedia', true)->pluck('nama', 'id'))
                        ->required()->live()
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $produk = Produk::find($state);
                            if ($produk) {
                                $set('harga_jual', $produk->harga);
                                $set('subtotal', $produk->harga * floatval($get('jumlah') ?: 1));
                            }
                        }),
                    TextInput::make('jumlah')->numeric()->required()->default(1)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $set('subtotal', floatval($get('jumlah')) * floatval($get('harga_jual')));
                        }),
                    TextInput::make('harga_jual')->numeric()->prefix('Rp')->readOnly(),
                    TextInput::make('subtotal')->numeric()->prefix('Rp')->readOnly(),
                ])->columns(4)->label('Item Pesanan')->addActionLabel('Tambah Item')->columnSpanFull(),
            Placeholder::make('total_label')
                ->label('Total Pembayaran')
                ->content(function (Get $get) {
                    $items = $get('detailPenjualan') ?? [];
                    $total = collect($items)->sum(fn($i) => floatval($i['subtotal'] ?? 0));
                    return 'Rp ' . number_format($total, 0, ',', '.');
                })->live()->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('kode_transaksi')->searchable()->label('Kode Transaksi'),
            TextColumn::make('tanggal')->date('d/m/Y')->sortable(),
            TextColumn::make('total')->money('IDR')->label('Total'),
            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn ($state) => $state === 'sudah_bayar' ? 'success' : 'warning')
                ->formatStateUsing(fn ($state) => $state === 'sudah_bayar' ? 'Sudah Bayar' : 'Belum Bayar'),
            TextColumn::make('bayar')
                ->money('IDR')
                ->label('Bayar')
                ->placeholder('-')
                ->formatStateUsing(fn ($state, $record) => $record->status === 'sudah_bayar' ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
            TextColumn::make('kembalian')
                ->money('IDR')
                ->label('Kembalian')
                ->formatStateUsing(fn ($state, $record) => $record->status === 'sudah_bayar' ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
            TextColumn::make('user.name')->label('Kasir'),
        ])
        ->defaultSort('id', 'desc')
        ->actions([
            Action::make('konfirmasi_bayar')
                ->label('Konfirmasi Pembayaran')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'belum_bayar')
                ->form([
                    TextInput::make('bayar')
                        ->label('Nominal Bayar')
                        ->numeric()->prefix('Rp')->required()
                        ->hint('Masukkan nominal tanpa titik. Contoh: 50000'),
                ])
                ->action(function ($record, array $data) {
                    $bayar = floatval($data['bayar']);
                    if ($bayar < $record->total) {
                        \Filament\Notifications\Notification::make()
                            ->title('Uang Kurang!')
                            ->body('Uang yang diberikan kurang. Segera cek kembali. Total: Rp ' . number_format($record->total, 0, ',', '.') . ', Bayar: Rp ' . number_format($bayar, 0, ',', '.'))
                            ->danger()
                            ->send();
                        return;
                    }
                    $record->update([
                        'bayar' => $bayar,
                        'kembalian' => $bayar - $record->total,
                        'status' => 'sudah_bayar',
                    ]);
                    // Kurangi stok setelah pembayaran dikonfirmasi
                    foreach ($record->detailPenjualan as $detail) {
                        $detail->produk->decrement('stok', $detail->jumlah);
                    }
                    \Filament\Notifications\Notification::make()
                        ->title('Pembayaran Berhasil!')
                        ->body('Kembalian: Rp ' . number_format($bayar - $record->total, 0, ',', '.'))
                        ->success()
                        ->send();
                }),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiPenjualans::route('/'),
            'create' => Pages\CreateTransaksiPenjualan::route('/create'),
            'edit' => Pages\EditTransaksiPenjualan::route('/{record}/edit'),
        ];
    }
}
