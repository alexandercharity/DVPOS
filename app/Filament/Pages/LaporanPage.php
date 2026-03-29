<?php

namespace App\Filament\Pages;

use App\Models\Pembelian;
use App\Models\TransaksiPenjualan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class LaporanPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $title = 'Laporan Penjualan & Pembelian';
    protected static ?string $navigationGroup = 'Laporan';
    protected static string $view = 'filament.pages.laporan-page';

    public static function canAccess(): bool
    {
        return true; // kasir & pemilik bisa akses
    }

    public ?string $tanggal_mulai = null;
    public ?string $tanggal_selesai = null;

    public function mount(): void
    {
        $this->tanggal_mulai = now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = now()->format('Y-m-d');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('tanggal_mulai')->label('Dari Tanggal')->required(),
            DatePicker::make('tanggal_selesai')->label('Sampai Tanggal')->required(),
        ])->columns(2);
    }

    public function getPenjualan()
    {
        return TransaksiPenjualan::with(['user', 'detailPenjualan.produk'])
            ->whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_selesai])
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function getPembelian()
    {
        return Pembelian::with(['supplier', 'detailPembelian.produk'])
            ->whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_selesai])
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function getTotalPenjualan()
    {
        if (auth()->user()->isKasir()) return null;
        return TransaksiPenjualan::whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_selesai])->sum('total');
    }

    public function getTotalPembelian()
    {
        if (auth()->user()->isKasir()) return null;
        return Pembelian::whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_selesai])->sum('total');
    }
}
