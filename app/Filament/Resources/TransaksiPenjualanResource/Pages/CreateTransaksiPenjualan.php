<?php

namespace App\Filament\Resources\TransaksiPenjualanResource\Pages;

use App\Filament\Resources\TransaksiPenjualanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksiPenjualan extends CreateRecord
{
    protected static string $resource = TransaksiPenjualanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'belum_bayar';
        $data['bayar'] = 0;
        $data['kembalian'] = 0;
        $data['total'] = 0;
        return $data;
    }

    protected function afterCreate(): void
    {
        // Hitung total saja, stok belum dikurangi
        $total = $this->record->detailPenjualan()->sum('subtotal');
        $this->record->updateQuietly(['total' => $total]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
