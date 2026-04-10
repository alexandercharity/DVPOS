<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiPenjualan;
use App\Models\Pembelian;
use Filament\Widgets\ChartWidget;

class PenjualanChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan & Pembelian (7 Hari Terakhir)';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->isPemilik();
    }

    protected function getData(): array
    {
        $labels = [];
        $penjualan = [];
        $pembelian = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            $penjualan[] = TransaksiPenjualan::whereDate('tanggal', $date)->sum('total');
            $pembelian[] = Pembelian::whereDate('tanggal', $date)->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan',
                    'data' => $penjualan,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34,197,94,0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Pembelian',
                    'data' => $pembelian,
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249,115,22,0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
