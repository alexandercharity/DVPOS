<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';
    protected $fillable = ['kategori_bahan_baku_id', 'nama', 'satuan', 'stok', 'stok_minimum', 'keterangan'];

    protected $casts = ['stok' => 'decimal:3', 'stok_minimum' => 'decimal:3'];

    public function kategoriBahanBaku()
    {
        return $this->belongsTo(KategoriBahanBaku::class);
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
