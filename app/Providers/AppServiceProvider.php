<?php

namespace App\Providers;

use App\Models\BahanBaku;
use App\Models\Pembelian;
use App\Models\TransaksiPenjualan;
use App\Observers\BahanBakuObserver;
use App\Observers\PembelianObserver;
use App\Observers\TransaksiPenjualanObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        BahanBaku::observe(BahanBakuObserver::class);
        Pembelian::observe(PembelianObserver::class);
        TransaksiPenjualan::observe(TransaksiPenjualanObserver::class);
    }
}
