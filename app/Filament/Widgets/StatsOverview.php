<?php

namespace App\Filament\Widgets;

use App\Models\BahanBaku;
use App\Models\TransaksiPenjualan;
use App\Models\Pembelian;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $totalPenjualanHari = TransaksiPenjualan::whereDate('tanggal', today())->sum('total');

        $stats = [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($totalPenjualanHari, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];

        if (auth()->user()->isPemilik()) {
            $totalPenjualanBulan = TransaksiPenjualan::whereMonth('tanggal', now()->month)->sum('total');
            $totalPembelianBulan = Pembelian::whereMonth('tanggal', now()->month)->sum('total');

            // Hitung stok rendah dengan raw query supaya lebih efisien
            $stokRendah = BahanBaku::whereRaw('stok <= stok_minimum')->count();

            $stats[] = Stat::make('Penjualan Bulan Ini', 'Rp ' . number_format($totalPenjualanBulan, 0, ',', '.'))
                ->icon('heroicon-o-chart-bar')
                ->color('info');
            $stats[] = Stat::make('Pembelian Bulan Ini', 'Rp ' . number_format($totalPembelianBulan, 0, ',', '.'))
                ->icon('heroicon-o-shopping-cart')
                ->color('warning');
            $stats[] = Stat::make('Bahan Baku Stok Rendah', $stokRendah . ' bahan')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stokRendah > 0 ? 'danger' : 'success');
        }

        return $stats;
    }
}
