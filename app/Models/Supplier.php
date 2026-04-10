<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $fillable = ['nama', 'telepon', 'email', 'alamat'];

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }
}
