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
}
