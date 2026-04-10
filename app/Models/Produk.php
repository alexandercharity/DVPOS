<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = ['kategori_id', 'nama', 'harga', 'stok', 'tersedia', 'gambar', 'deskripsi'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class);
    }

    public function resep()
    {
        return $this->hasMany(Resep::class);
    }

    // Hitung estimasi porsi yang bisa dibuat dari stok bahan baku
    public function getEstimasiPorsiAttribute(): ?int
    {
        $resep = $this->resep()->with('bahanBaku')->get();
        if ($resep->isEmpty()) return null;

        $minPorsi = PHP_INT_MAX;
        foreach ($resep as $r) {
            if (!$r->bahanBaku || $r->jumlah <= 0) continue;
            $stokBase = \App\Observers\BahanBakuObserver::convertToBase($r->bahanBaku->stok, $r->bahanBaku->satuan);
            $kebutuhanBase = \App\Observers\BahanBakuObserver::convertToBase($r->jumlah, $r->satuan);
            $porsi = $kebutuhanBase > 0 ? floor($stokBase / $kebutuhanBase) : 0;
            if ($porsi < $minPorsi) $minPorsi = $porsi;
        }

        return $minPorsi === PHP_INT_MAX ? 0 : (int) $minPorsi;
    }
}
