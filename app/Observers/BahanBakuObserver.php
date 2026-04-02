<?php

namespace App\Observers;

use App\Models\BahanBaku;
use App\Models\Resep;

class BahanBakuObserver
{
    // Konversi ke satuan dasar (gram atau ml)
    private static array $toBase = [
        'gram'    => 1,
        'kg'      => 1000,
        'liter'   => 1000,
        'ml'      => 1,
        'pcs'     => 1,
        'butir'   => 1,
        'ikat'    => 1,
        'bungkus' => 1,
    ];

    public static function convertToBase(float $value, string $satuan): float
    {
        $multiplier = self::$toBase[strtolower($satuan)] ?? 1;
        return $value * $multiplier;
    }

    public function updated(BahanBaku $bahanBaku): void
    {
        if (!$bahanBaku->isDirty('stok')) return;

        $produkIds = Resep::where('bahan_baku_id', $bahanBaku->id)
            ->pluck('produk_id')
            ->unique();

        foreach ($produkIds as $produkId) {
            static::updateStokProduk($produkId);
        }
    }

    public static function updateStokProduk(int $produkId): void
    {
        $resep = Resep::with('bahanBaku')
            ->where('produk_id', $produkId)
            ->get();

        if ($resep->isEmpty()) return;

        $minPorsi = PHP_INT_MAX;
        foreach ($resep as $r) {
            if (!$r->bahanBaku || $r->jumlah <= 0) continue;

            // Konversi stok bahan baku ke satuan dasar
            $stokBase = self::convertToBase($r->bahanBaku->stok, $r->bahanBaku->satuan);
            // Konversi kebutuhan resep ke satuan dasar
            $kebutuhanBase = self::convertToBase($r->jumlah, $r->satuan);

            $porsi = $kebutuhanBase > 0 ? floor($stokBase / $kebutuhanBase) : 0;
            if ($porsi < $minPorsi) $minPorsi = $porsi;
        }

        $stok = $minPorsi === PHP_INT_MAX ? 0 : (int) $minPorsi;
        \App\Models\Produk::where('id', $produkId)->update(['stok' => $stok]);
    }
}
