<?php

namespace App\Observers;

use App\Models\Pembelian;

class PembelianObserver
{
    public function created(Pembelian $pembelian): void
    {
        $total = 0;
        foreach ($pembelian->detailPembelian as $detail) {
            $total += $detail->subtotal;
            // Tambah stok bahan baku (bukan produk)
            $detail->bahanBaku->increment('stok', $detail->jumlah);
        }
        $pembelian->updateQuietly(['total' => $total]);
    }

    public function updated(Pembelian $pembelian): void
    {
        // Hanya recalculate total, jangan touch stok saat update
        if (!$pembelian->isDirty(['total', 'status'])) {
            $total = $pembelian->detailPembelian->sum('subtotal');
            $pembelian->updateQuietly(['total' => $total]);
        }
    }
}
