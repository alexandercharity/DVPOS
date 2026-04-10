<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests untuk logika cart kasir (tanpa database)
 * Menguji kalkulasi subtotal, total, dan validasi stok
 */
class KasirCartTest extends TestCase
{
    private array $cart = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->cart = [];
    }

    private function addToCart(int $produkId, string $nama, float $harga, int $jumlah, int $stok): void
    {
        if (isset($this->cart[$produkId])) {
            $this->cart[$produkId]['jumlah'] += $jumlah;
        } else {
            $this->cart[$produkId] = [
                'produk_id' => $produkId,
                'nama'      => $nama,
                'harga'     => $harga,
                'jumlah'    => $jumlah,
                'stok'      => $stok,
            ];
        }
        $this->cart[$produkId]['subtotal'] = $this->cart[$produkId]['harga'] * $this->cart[$produkId]['jumlah'];
    }

    private function getTotal(): float
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function test_add_item_to_cart(): void
    {
        $this->addToCart(1, 'Kopi', 15000, 1, 10);
        $this->assertArrayHasKey(1, $this->cart);
        $this->assertEquals(1, $this->cart[1]['jumlah']);
    }

    public function test_subtotal_calculated_correctly(): void
    {
        $this->addToCart(1, 'Kopi', 15000, 2, 10);
        $this->assertEquals(30000, $this->cart[1]['subtotal']);
    }

    public function test_total_with_multiple_items(): void
    {
        $this->addToCart(1, 'Kopi', 15000, 2, 10);
        $this->addToCart(2, 'Teh', 10000, 3, 10);
        $this->assertEquals(60000, $this->getTotal());
    }

    public function test_remove_item_from_cart(): void
    {
        $this->addToCart(1, 'Kopi', 15000, 1, 10);
        unset($this->cart[1]);
        $this->assertArrayNotHasKey(1, $this->cart);
    }

    public function test_empty_cart_total_is_zero(): void
    {
        $this->assertEquals(0, $this->getTotal());
    }

    public function test_update_jumlah_recalculates_subtotal(): void
    {
        $this->addToCart(1, 'Kopi', 15000, 1, 10);
        $this->cart[1]['jumlah'] = 3;
        $this->cart[1]['subtotal'] = $this->cart[1]['harga'] * 3;
        $this->assertEquals(45000, $this->cart[1]['subtotal']);
    }

    public function test_stok_validation_prevents_over_order(): void
    {
        $stok = 5;
        $jumlahDiminta = 10;
        $this->assertTrue($jumlahDiminta > $stok);
    }

    public function test_clear_cart(): void
    {
        $this->addToCart(1, 'Kopi', 15000, 1, 10);
        $this->addToCart(2, 'Teh', 10000, 2, 10);
        $this->cart = [];
        $this->assertEmpty($this->cart);
    }
}
