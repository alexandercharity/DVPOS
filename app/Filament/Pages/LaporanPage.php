<?php

namespace App\Filament\Pages;

use App\Models\Pembelian;
use App\Models\TransaksiPenjualan;
use Filament\Pages\Page;

class LaporanPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $title = 'Laporan Penjualan & Pembelian';
    protected static ?string $navigationGroup = 'Laporan';
    protected static string $view = 'filament.pages.laporan-page';

    public static function canAccess(): bool
    {
        return true;
    }

    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';

    public function mount(): void
    {
        $this->tanggal_mulai = now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = now()->format('Y-m-d');
    }

    public function getPenjualan()
    {
        return TransaksiPenjualan::with(['user'])
            ->whereDate('tanggal', '>=', $this->tanggal_mulai)
            ->whereDate('tanggal', '<=', $this->tanggal_selesai)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function getPembelian()
    {
        return Pembelian::with(['supplier'])
            ->whereDate('tanggal', '>=', $this->tanggal_mulai)
            ->whereDate('tanggal', '<=', $this->tanggal_selesai)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function getTotalPenjualan(): float
    {
        if (auth()->user()->isKasir()) return 0;
        return (float) TransaksiPenjualan::whereDate('tanggal', '>=', $this->tanggal_mulai)
            ->whereDate('tanggal', '<=', $this->tanggal_selesai)
            ->sum('total');
    }

    public function getTotalPembelian(): float
    {
        if (auth()->user()->isKasir()) return 0;
        return (float) Pembelian::whereDate('tanggal', '>=', $this->tanggal_mulai)
            ->whereDate('tanggal', '<=', $this->tanggal_selesai)
            ->sum('total');
    }
}
