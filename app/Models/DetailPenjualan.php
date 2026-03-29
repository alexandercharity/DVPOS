<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';
    protected $fillable = ['transaksi_penjualan_id', 'produk_id', 'jumlah', 'harga_jual', 'subtotal'];

    public function transaksiPenjualan()
    {
        return $this->belongsTo(TransaksiPenjualan::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
