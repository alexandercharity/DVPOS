<?php

namespace App\Observers;

use App\Models\TransaksiPenjualan;

class TransaksiPenjualanObserver
{
    public function created(TransaksiPenjualan $transaksi): void
    {
        // Hanya hitung total, stok dikurangi saat konfirmasi bayar
        $total = $transaksi->detailPenjualan->sum('subtotal');
        $transaksi->updateQuietly(['total' => $total]);
    }

    public function updated(TransaksiPenjualan $transaksi): void
    {
        // Recalculate total saat ada perubahan
        if (!$transaksi->isDirty(['total', 'bayar', 'kembalian', 'status'])) {
            $total = $transaksi->detailPenjualan->sum('subtotal');
            $transaksi->updateQuietly(['total' => $total]);
        }
    }
}
