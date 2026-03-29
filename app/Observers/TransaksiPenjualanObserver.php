<?php

namespace App\Observers;

use App\Models\TransaksiPenjualan;

class TransaksiPenjualanObserver
{
    public function created(TransaksiPenjualan $transaksi): void
    {
        // Hitung total dan kurangi stok
        $total = 0;
        foreach ($transaksi->detailPenjualan as $detail) {
            $total += $detail->subtotal;
            $detail->produk->decrement('stok', $detail->jumlah);
        }
        $transaksi->updateQuietly(['total' => $total]);
    }

    public function updated(TransaksiPenjualan $transaksi): void
    {
        $total = $transaksi->detailPenjualan->sum('subtotal');
        $transaksi->updateQuietly(['total' => $total]);
    }
}
