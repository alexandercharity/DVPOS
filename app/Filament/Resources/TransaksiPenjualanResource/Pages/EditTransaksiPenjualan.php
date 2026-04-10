<?php

namespace App\Filament\Resources\TransaksiPenjualanResource\Pages;

use App\Filament\Resources\TransaksiPenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiPenjualan extends EditRecord
{
    protected static string $resource = TransaksiPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    // Kembalikan stok hanya jika transaksi sudah dibayar
                    if ($this->record->status === 'sudah_bayar') {
                        foreach ($this->record->detailPenjualan as $detail) {
                            $detail->produk->increment('stok', $detail->jumlah);
                        }
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $total = $this->record->detailPenjualan()->sum('subtotal');
        $this->record->updateQuietly(['total' => $total]);
    }
}
