<?php

namespace App\Filament\Widgets;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\TransaksiPenjualan;
use App\Models\Pembelian;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return true; // semua role bisa lihat
    }
    protected function getStats(): array
    {
        $totalPenjualanHari = TransaksiPenjualan::whereDate('tanggal', today())->sum('total');
        $totalPenjualanBulan = TransaksiPenjualan::whereMonth('tanggal', now()->month)->sum('total');
        $stokRendah = BahanBaku::where('stok', '<=', 5)->count();

        $stats = [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($totalPenjualanHari, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];

        if (auth()->user()->isPemilik()) {
            $totalPembelianBulan = Pembelian::whereMonth('tanggal', now()->month)->sum('total');
            $stats[] = Stat::make('Penjualan Bulan Ini', 'Rp ' . number_format($totalPenjualanBulan, 0, ',', '.'))
                ->icon('heroicon-o-chart-bar')
                ->color('info');
            $stats[] = Stat::make('Pembelian Bulan Ini', 'Rp ' . number_format($totalPembelianBulan, 0, ',', '.'))
                ->icon('heroicon-o-shopping-cart')
                ->color('warning');
            $stats[] = Stat::make('Produk Stok Rendah', $stokRendah . ' produk')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stokRendah > 0 ? 'danger' : 'success');
        }

        return $stats;
    }
}
