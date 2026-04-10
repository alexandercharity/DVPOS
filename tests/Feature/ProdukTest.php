<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdukTest extends TestCase
{
    use RefreshDatabase;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kategori = Kategori::create(['nama' => 'Minuman']);
    }

    public function test_create_produk(): void
    {
        $produk = Produk::create([
            'kategori_id' => $this->kategori->id,
            'nama'        => 'Kopi Susu',
            'harga'       => 20000,
            'stok'        => 10,
            'tersedia'    => true,
        ]);

        $this->assertDatabaseHas('produk', ['nama' => 'Kopi Susu']);
    }

    public function test_produk_belongs_to_kategori(): void
    {
        $produk = Produk::create([
            'kategori_id' => $this->kategori->id,
            'nama'        => 'Teh Manis',
            'harga'       => 10000,
            'stok'        => 5,
            'tersedia'    => true,
        ]);

        $this->assertEquals('Minuman', $produk->kategori->nama);
    }

    public function test_produk_tersedia_default_true(): void
    {
        $produk = Produk::create([
            'kategori_id' => $this->kategori->id,
            'nama'        => 'Es Jeruk',
            'harga'       => 12000,
            'stok'        => 8,
            'tersedia'    => true,
        ]);

        $this->assertTrue((bool) $produk->tersedia);
    }

    public function test_produk_tidak_tersedia(): void
    {
        $produk = Produk::create([
            'kategori_id' => $this->kategori->id,
            'nama'        => 'Jus Alpukat',
            'harga'       => 18000,
            'stok'        => 0,
            'tersedia'    => false,
        ]);

        $this->assertFalse((bool) $produk->tersedia);
    }

    public function test_filter_produk_tersedia_dan_ada_stok(): void
    {
        Produk::create(['kategori_id' => $this->kategori->id, 'nama' => 'A', 'harga' => 10000, 'stok' => 5, 'tersedia' => true]);
        Produk::create(['kategori_id' => $this->kategori->id, 'nama' => 'B', 'harga' => 10000, 'stok' => 0, 'tersedia' => true]);
        Produk::create(['kategori_id' => $this->kategori->id, 'nama' => 'C', 'harga' => 10000, 'stok' => 3, 'tersedia' => false]);

        $result = Produk::where('stok', '>', 0)->where('tersedia', true)->get();
        $this->assertCount(1, $result);
        $this->assertEquals('A', $result->first()->nama);
    }
}
