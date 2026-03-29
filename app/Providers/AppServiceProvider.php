<?php

namespace App\Providers;

use App\Models\Pembelian;
use App\Models\TransaksiPenjualan;
use App\Observers\PembelianObserver;
use App\Observers\TransaksiPenjualanObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Pembelian::observe(PembelianObserver::class);
        TransaksiPenjualan::observe(TransaksiPenjualanObserver::class);
    }
}
