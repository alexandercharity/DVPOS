<?php

namespace Tests\Unit;

use App\Observers\BahanBakuObserver;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests untuk kalkulasi estimasi porsi berdasarkan stok bahan baku
 */
class StokPorsiCalculationTest extends TestCase
{
    /**
     * Simulasi logika kalkulasi porsi dari BahanBakuObserver::updateStokProduk
     */
    private function hitungMinPorsi(array $resepItems): int
    {
        if (empty($resepItems)) return 0;

        $minPorsi = PHP_INT_MAX;
        foreach ($resepItems as $item) {
            $stokBase      = BahanBakuObserver::convertToBase($item['stok'], $item['satuan_bahan']);
            $kebutuhanBase = BahanBakuObserver::convertToBase($item['jumlah'], $item['satuan_resep']);
            $porsi         = $kebutuhanBase > 0 ? floor($stokBase / $kebutuhanBase) : 0;
            if ($porsi < $minPorsi) $minPorsi = $porsi;
        }

        return $minPorsi === PHP_INT_MAX ? 0 : (int) $minPorsi;
    }

    public function test_porsi_dengan_satu_bahan(): void
    {
        $resep = [
            ['stok' => 1000, 'satuan_bahan' => 'gram', 'jumlah' => 200, 'satuan_resep' => 'gram'],
        ];
        $this->assertEquals(5, $this->hitungMinPorsi($resep));
    }

    public function test_porsi_dibatasi_bahan_paling_sedikit(): void
    {
        $resep = [
            ['stok' => 1000, 'satuan_bahan' => 'gram', 'jumlah' => 100, 'satuan_resep' => 'gram'],
            ['stok' => 500,  'satuan_bahan' => 'ml',   'jumlah' => 250, 'satuan_resep' => 'ml'],
        ];
        // bahan 1: 10 porsi, bahan 2: 2 porsi → min = 2
        $this->assertEquals(2, $this->hitungMinPorsi($resep));
    }

    public function test_porsi_dengan_konversi_kg_ke_gram(): void
    {
        $resep = [
            ['stok' => 1, 'satuan_bahan' => 'kg', 'jumlah' => 200, 'satuan_resep' => 'gram'],
        ];
        // 1 kg = 1000 gram, butuh 200 gram/porsi → 5 porsi
        $this->assertEquals(5, $this->hitungMinPorsi($resep));
    }

    public function test_porsi_nol_jika_stok_habis(): void
    {
        $resep = [
            ['stok' => 0, 'satuan_bahan' => 'gram', 'jumlah' => 100, 'satuan_resep' => 'gram'],
        ];
        $this->assertEquals(0, $this->hitungMinPorsi($resep));
    }

    public function test_porsi_nol_jika_resep_kosong(): void
    {
        $this->assertEquals(0, $this->hitungMinPorsi([]));
    }

    public function test_porsi_dengan_liter_dan_ml(): void
    {
        $resep = [
            ['stok' => 1, 'satuan_bahan' => 'liter', 'jumlah' => 250, 'satuan_resep' => 'ml'],
        ];
        // 1 liter = 1000 ml, butuh 250 ml/porsi → 4 porsi
        $this->assertEquals(4, $this->hitungMinPorsi($resep));
    }
}
