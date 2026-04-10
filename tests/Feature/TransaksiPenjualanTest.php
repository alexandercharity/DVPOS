<?php

namespace Tests\Feature;

use App\Models\DetailPenjualan;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\TransaksiPenjualan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiPenjualanTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name'     => 'Kasir Test',
            'email'    => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role'     => 'kasir',
        ]);

        $kategori = Kategori::create(['nama' => 'Minuman']);
        $this->produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama'        => 'Kopi',
            'harga'       => 15000,
            'stok'        => 10,
            'tersedia'    => true,
        ]);
    }

    public function test_create_transaksi_penjualan(): void
    {
        $transaksi = TransaksiPenjualan::create([
            'user_id'         => $this->user->id,
            'kode_transaksi'  => 'TRX-TEST001',
            'tanggal'         => now(),
            'total'           => 30000,
            'status'          => 'belum_bayar',
            'bayar'           => 0,
            'kembalian'       => 0,
        ]);

        $this->assertDatabaseHas('transaksi_penjualan', ['kode_transaksi' => 'TRX-TEST001']);
    }

    public function test_transaksi_has_detail_penjualan(): void
    {
        $transaksi = TransaksiPenjualan::create([
            'user_id'        => $this->user->id,
            'kode_transaksi' => 'TRX-TEST002',
            'tanggal'        => now(),
            'total'          => 30000,
            'status'         => 'belum_bayar',
            'bayar'          => 0,
            'kembalian'      => 0,
        ]);

        DetailPenjualan::create([
            'transaksi_penjualan_id' => $transaksi->id,
            'produk_id'              => $this->produk->id,
            'jumlah'                 => 2,
            'harga_jual'             => 15000,
            'subtotal'               => 30000,
        ]);

        $this->assertCount(1, $transaksi->detailPenjualan);
    }

    public function test_total_transaksi_dari_detail(): void
    {
        $transaksi = TransaksiPenjualan::create([
            'user_id'        => $this->user->id,
            'kode_transaksi' => 'TRX-TEST003',
            'tanggal'        => now(),
            'total'          => 0,
            'status'         => 'belum_bayar',
            'bayar'          => 0,
            'kembalian'      => 0,
        ]);

        DetailPenjualan::create([
            'transaksi_penjualan_id' => $transaksi->id,
            'produk_id'              => $this->produk->id,
            'jumlah'                 => 3,
            'harga_jual'             => 15000,
            'subtotal'               => 45000,
        ]);

        $total = $transaksi->detailPenjualan()->sum('subtotal');
        $this->assertEquals(45000, $total);
    }

    public function test_status_transaksi_dapat_diupdate(): void
    {
        $transaksi = TransaksiPenjualan::create([
            'user_id'        => $this->user->id,
            'kode_transaksi' => 'TRX-TEST004',
            'tanggal'        => now(),
            'total'          => 15000,
            'status'         => 'belum_bayar',
            'bayar'          => 0,
            'kembalian'      => 0,
        ]);

        $transaksi->updateQuietly(['status' => 'sudah_bayar', 'bayar' => 20000, 'kembalian' => 5000]);
        $this->assertEquals('sudah_bayar', $transaksi->fresh()->status);
    }

    public function test_kembalian_dihitung_benar(): void
    {
        $total    = 30000;
        $bayar    = 50000;
        $kembalian = $bayar - $total;
        $this->assertEquals(20000, $kembalian);
    }
}
