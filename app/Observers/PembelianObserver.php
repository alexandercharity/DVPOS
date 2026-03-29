<?php

namespace App\Observers;

use App\Models\Pembelian;

class PembelianObserver
{
    public function created(Pembelian $pembelian): void
    {
        // Hitung total dan tambah stok
        $total = 0;
        foreach ($pembelian->detailPembelian as $detail) {
            $total += $detail->subtotal;
            $detail->produk->increment('stok', $detail->jumlah);
        }
        $pembelian->updateQuietly(['total' => $total]);
    }

    public function updated(Pembelian $pembelian): void
    {
        $total = $pembelian->detailPembelian->sum('subtotal');
        $pembelian->updateQuietly(['total' => $total]);
    }
}
