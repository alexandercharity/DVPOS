<?php

namespace App\Observers;

use App\Models\TransaksiPenjualan;

class TransaksiPenjualanObserver
{
    public function created(TransaksiPenjualan $transaksi): void
    {
        // Total dihandle di CreateTransaksiPenjualan::afterCreate()
    }

    public function updated(TransaksiPenjualan $transaksi): void
    {
        if ($transaksi->isDirty(['total', 'bayar', 'kembalian', 'status'])) {
            return;
        }
        $total = $transaksi->detailPenjualan->sum('subtotal');
        $transaksi->updateQuietly(['total' => $total]);
    }
}
