<?php

namespace Tests\Feature;

use App\Models\BahanBaku;
use App\Models\KategoriBahanBaku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BahanBakuTest extends TestCase
{
    use RefreshDatabase;

    private KategoriBahanBaku $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kategori = KategoriBahanBaku::create(['nama' => 'Bumbu']);
    }

    public function test_create_bahan_baku(): void
    {
        $bahan = BahanBaku::create([
            'kategori_bahan_baku_id' => $this->kategori->id,
            'nama'          => 'Gula',
            'satuan'        => 'kg',
            'stok'          => 5.0,
            'stok_minimum'  => 1.0,
        ]);

        $this->assertDatabaseHas('bahan_baku', ['nama' => 'Gula']);
        $this->assertEquals(5.0, $bahan->stok);
    }

    public function test_bahan_baku_belongs_to_kategori(): void
    {
        $bahan = BahanBaku::create([
            'kategori_bahan_baku_id' => $this->kategori->id,
            'nama'   => 'Garam',
            'satuan' => 'gram',
            'stok'   => 500,
        ]);

        $this->assertEquals('Bumbu', $bahan->kategoriBahanBaku->nama);
    }

    public function test_stok_minimum_default_null(): void
    {
        $bahan = BahanBaku::create([
            'kategori_bahan_baku_id' => $this->kategori->id,
            'nama'   => 'Merica',
            'satuan' => 'gram',
            'stok'   => 100,
        ]);

        $this->assertNull($bahan->stok_minimum);
    }

    public function test_update_stok_bahan_baku(): void
    {
        $bahan = BahanBaku::create([
            'kategori_bahan_baku_id' => $this->kategori->id,
            'nama'   => 'Tepung',
            'satuan' => 'kg',
            'stok'   => 2.0,
        ]);

        $bahan->update(['stok' => 5.0]);
        $this->assertEquals(5.0, $bahan->fresh()->stok);
    }

    public function test_delete_bahan_baku(): void
    {
        $bahan = BahanBaku::create([
            'kategori_bahan_baku_id' => $this->kategori->id,
            'nama'   => 'Minyak',
            'satuan' => 'liter',
            'stok'   => 3.0,
        ]);

        $bahan->delete();
        $this->assertDatabaseMissing('bahan_baku', ['nama' => 'Minyak']);
    }
}
