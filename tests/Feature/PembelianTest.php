<?php

namespace Tests\Feature;

use App\Models\BahanBaku;
use App\Models\DetailPembelian;
use App\Models\KategoriBahanBaku;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Supplier $supplier;
    private BahanBaku $bahanBaku;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'role'     => 'pemilik',
        ]);

        $this->supplier = Supplier::create([
            'nama'     => 'Supplier ABC',
            'telepon'  => '08123456789',
            'email'    => 'supplier@test.com',
            'alamat'   => 'Jl. Test No. 1',
        ]);

        $kategori = KategoriBahanBaku::create(['nama' => 'Bahan Pokok']);
        $this->bahanBaku = BahanBaku::create([
            'kategori_bahan_baku_id' => $kategori->id,
            'nama'   => 'Gula',
            'satuan' => 'kg',
            'stok'   => 0,
        ]);
    }

    public function test_create_pembelian(): void
    {
        $pembelian = Pembelian::create([
            'supplier_id' => $this->supplier->id,
            'user_id'     => $this->user->id,
            'tanggal'     => now(),
            'status'      => 'lunas',
            'total'       => 50000,
        ]);

        $this->assertDatabaseHas('pembelian', ['status' => 'lunas']);
    }

    public function test_pembelian_belongs_to_supplier(): void
    {
        $pembelian = Pembelian::create([
            'supplier_id' => $this->supplier->id,
            'user_id'     => $this->user->id,
            'tanggal'     => now(),
            'status'      => 'lunas',
            'total'       => 50000,
        ]);

        $this->assertEquals('Supplier ABC', $pembelian->supplier->nama);
    }

    public function test_detail_pembelian_created(): void
    {
        $pembelian = Pembelian::create([
            'supplier_id' => $this->supplier->id,
            'user_id'     => $this->user->id,
            'tanggal'     => now(),
            'status'      => 'lunas',
            'total'       => 50000,
        ]);

        DetailPembelian::create([
            'pembelian_id'  => $pembelian->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'jumlah'        => 5,
            'harga_beli'    => 10000,
            'subtotal'      => 50000,
        ]);

        $this->assertCount(1, $pembelian->detailPembelian);
    }

    public function test_total_pembelian_dari_detail(): void
    {
        $pembelian = Pembelian::create([
            'supplier_id' => $this->supplier->id,
            'user_id'     => $this->user->id,
            'tanggal'     => now(),
            'status'      => 'belum_lunas',
            'total'       => 0,
        ]);

        DetailPembelian::create([
            'pembelian_id'  => $pembelian->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'jumlah'        => 5,
            'harga_beli'    => 10000,
            'subtotal'      => 50000,
        ]);

        $total = $pembelian->detailPembelian()->sum('subtotal');
        $this->assertEquals(50000, $total);
    }
}
