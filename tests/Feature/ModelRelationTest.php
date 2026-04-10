<?php

namespace Tests\Feature;

use App\Models\BahanBaku;
use App\Models\DetailPenjualan;
use App\Models\Kategori;
use App\Models\KategoriBahanBaku;
use App\Models\Produk;
use App\Models\Resep;
use App\Models\Supplier;
use App\Models\TransaksiPenjualan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_kategori_has_many_produk(): void
    {
        $kategori = Kategori::create(['nama' => 'Makanan']);
        Produk::create(['kategori_id' => $kategori->id, 'nama' => 'Nasi', 'harga' => 15000, 'stok' => 5, 'tersedia' => true]);

        $this->assertCount(1, $kategori->produk);
    }

    public function test_kategori_bahan_baku_has_many_bahan_baku(): void
    {
        $kategori = KategoriBahanBaku::create(['nama' => 'Rempah']);
        BahanBaku::create(['kategori_bahan_baku_id' => $kategori->id, 'nama' => 'Jahe', 'satuan' => 'gram', 'stok' => 200]);

        $this->assertCount(1, $kategori->bahanBaku);
    }

    public function test_supplier_has_many_pembelian(): void
    {
        $supplier = Supplier::create(['nama' => 'Toko Bahan', 'telepon' => '08111', 'email' => 'toko@test.com', 'alamat' => 'Jl. A']);
        $user = User::create(['name' => 'Admin', 'email' => 'a@a.com', 'password' => bcrypt('pass'), 'role' => 'pemilik']);

        \App\Models\Pembelian::create([
            'supplier_id' => $supplier->id,
            'user_id'     => $user->id,
            'tanggal'     => now(),
            'status'      => 'lunas',
            'total'       => 10000,
        ]);

        $this->assertCount(1, $supplier->pembelian);
    }

    public function test_resep_belongs_to_produk_and_bahan_baku(): void
    {
        $kategori = Kategori::create(['nama' => 'Minuman']);
        $produk = Produk::create(['kategori_id' => $kategori->id, 'nama' => 'Kopi', 'harga' => 15000, 'stok' => 0, 'tersedia' => true]);

        $kategoriBahan = KategoriBahanBaku::create(['nama' => 'Biji']);
        $bahan = BahanBaku::create(['kategori_bahan_baku_id' => $kategoriBahan->id, 'nama' => 'Kopi Bubuk', 'satuan' => 'gram', 'stok' => 500]);

        $resep = Resep::create(['produk_id' => $produk->id, 'bahan_baku_id' => $bahan->id, 'jumlah' => 20, 'satuan' => 'gram']);

        $this->assertEquals('Kopi', $resep->produk->nama);
        $this->assertEquals('Kopi Bubuk', $resep->bahanBaku->nama);
    }

    public function test_produk_has_many_resep(): void
    {
        $kategori = Kategori::create(['nama' => 'Minuman']);
        $produk = Produk::create(['kategori_id' => $kategori->id, 'nama' => 'Teh', 'harga' => 10000, 'stok' => 0, 'tersedia' => true]);

        $kategoriBahan = KategoriBahanBaku::create(['nama' => 'Daun']);
        $bahan = BahanBaku::create(['kategori_bahan_baku_id' => $kategoriBahan->id, 'nama' => 'Teh Daun', 'satuan' => 'gram', 'stok' => 300]);

        Resep::create(['produk_id' => $produk->id, 'bahan_baku_id' => $bahan->id, 'jumlah' => 5, 'satuan' => 'gram']);

        $this->assertCount(1, $produk->resep);
    }

    public function test_detail_penjualan_belongs_to_produk(): void
    {
        $user = User::create(['name' => 'Kasir', 'email' => 'k@k.com', 'password' => bcrypt('pass'), 'role' => 'kasir']);
        $kategori = Kategori::create(['nama' => 'Minuman']);
        $produk = Produk::create(['kategori_id' => $kategori->id, 'nama' => 'Es Teh', 'harga' => 8000, 'stok' => 5, 'tersedia' => true]);

        $transaksi = TransaksiPenjualan::create([
            'user_id' => $user->id, 'kode_transaksi' => 'TRX-X1',
            'tanggal' => now(), 'total' => 8000, 'status' => 'belum_bayar', 'bayar' => 0, 'kembalian' => 0,
        ]);

        $detail = DetailPenjualan::create([
            'transaksi_penjualan_id' => $transaksi->id,
            'produk_id' => $produk->id,
            'jumlah' => 1, 'harga_jual' => 8000, 'subtotal' => 8000,
        ]);

        $this->assertEquals('Es Teh', $detail->produk->nama);
    }

    public function test_bahan_baku_has_many_resep(): void
    {
        $kategoriBahan = KategoriBahanBaku::create(['nama' => 'Susu']);
        $bahan = BahanBaku::create(['kategori_bahan_baku_id' => $kategoriBahan->id, 'nama' => 'Susu Segar', 'satuan' => 'ml', 'stok' => 1000]);

        $kategori = Kategori::create(['nama' => 'Minuman']);
        $produk = Produk::create(['kategori_id' => $kategori->id, 'nama' => 'Susu Hangat', 'harga' => 12000, 'stok' => 0, 'tersedia' => true]);

        Resep::create(['produk_id' => $produk->id, 'bahan_baku_id' => $bahan->id, 'jumlah' => 200, 'satuan' => 'ml']);

        $this->assertCount(1, $bahan->resep);
    }

    public function test_user_has_many_transaksi_penjualan(): void
    {
        $user = User::create(['name' => 'Kasir2', 'email' => 'k2@k.com', 'password' => bcrypt('pass'), 'role' => 'kasir']);

        TransaksiPenjualan::create([
            'user_id' => $user->id, 'kode_transaksi' => 'TRX-Y1',
            'tanggal' => now(), 'total' => 20000, 'status' => 'sudah_bayar', 'bayar' => 20000, 'kembalian' => 0,
        ]);

        $this->assertCount(1, $user->transaksiPenjualan);
    }
}
