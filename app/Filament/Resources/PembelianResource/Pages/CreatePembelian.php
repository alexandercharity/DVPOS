<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use App\Models\DetailPembelian;
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
        // Jalankan setelah semua tersimpan via dispatch ke end of request
        $pembelianId = $this->record->id;

        app()->terminating(function () use ($pembelianId) {
            $details = DetailPembelian::with('bahanBaku')
                ->where('pembelian_id', $pembelianId)
                ->get();

            $total = $details->sum('subtotal');

            \App\Models\Pembelian::where('id', $pembelianId)
                ->update(['total' => $total]);

            foreach ($details as $detail) {
                if ($detail->bahanBaku) {
                    $detail->bahanBaku->increment('stok', $detail->jumlah);
                }
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
