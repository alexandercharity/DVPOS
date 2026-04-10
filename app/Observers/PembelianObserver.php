<?php

namespace App\Observers;

use App\Models\Pembelian;

class PembelianObserver
{
    public function created(Pembelian $pembelian): void
    {
        // Stok dan total dihandle di CreatePembelian::afterCreate()
        // supaya detail sudah tersedia saat dihitung
    }

    public function updated(Pembelian $pembelian): void
    {
        if ($pembelian->isDirty(['total', 'status'])) {
            return;
        }
        $total = $pembelian->detailPembelian->sum('subtotal');
        $pembelian->updateQuietly(['total' => $total]);
    }
}
