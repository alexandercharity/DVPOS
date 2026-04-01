<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';
    protected $fillable = ['nama', 'satuan', 'stok', 'keterangan'];

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class);
    }

    public function resep()
    {
        return $this->hasMany(Resep::class);
    }
}
