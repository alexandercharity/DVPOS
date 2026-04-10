<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    protected $table = 'detail_pembelian';
    protected $fillable = ['pembelian_id', 'bahan_baku_id', 'jumlah', 'harga_beli', 'subtotal'];

    protected $casts = ['jumlah' => 'float'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
