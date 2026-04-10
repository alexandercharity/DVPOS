<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBahanBaku extends Model
{
    protected $table = 'kategori_bahan_baku';
    protected $fillable = ['nama'];

    public function bahanBaku()
    {
        return $this->hasMany(BahanBaku::class);
    }
}
