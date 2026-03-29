<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePembelian extends CreateRecord
{
    protected static string $resource = PembelianResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['total'] = 0;
        $data['status'] = 'belum_lunas';
        return $data;
    }

    protected function afterCreate(): void
    {
        $total = $this->record->detailPembelian()->sum('subtotal');
        $this->record->updateQuietly(['total' => $total]);

        // Tambah stok
        foreach ($this->record->detailPembelian as $detail) {
            $detail->produk->increment('stok', $detail->jumlah);
        }
    }
}
