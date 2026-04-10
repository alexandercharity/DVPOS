# DVPOS - Digital Voucher Point of Sale

Sistem kasir berbasis web untuk manajemen penjualan, pembelian bahan baku, dan stok produk. Dibangun dengan Laravel 12 + Filament 3.

![CI Pipeline](https://github.com/alexandercharity/DVPOS/actions/workflows/ci.yml/badge.svg)

---

## Deskripsi Aplikasi

DVPOS adalah sistem manajemen restoran/kafe yang mencakup:
- **Kasir (POS)** — input pesanan, manajemen keranjang, proses transaksi
- **Manajemen Produk** — produk, kategori, resep berbasis bahan baku
- **Manajemen Bahan Baku** — stok otomatis dihitung dari resep
- **Pembelian** — pencatatan pembelian bahan baku dari supplier
- **Laporan** — laporan penjualan & pembelian berdasarkan rentang tanggal
- **Role-based Access** — pemilik dan kasir dengan hak akses berbeda

---

## Cara Menjalankan Aplikasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM

### Instalasi

```bash
# Clone repository
git clone https://github.com/alexandercharity/DVPOS.git
cd DVPOS

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Jalankan migrasi
php artisan migrate

# Build assets
npm run build

# Jalankan server
php artisan serve
```

Akses aplikasi di `http://localhost:8000/admin`

---

## Cara Menjalankan Test

```bash
# Jalankan semua test
php artisan test

# Jalankan dengan laporan coverage
php artisan test --coverage

# Jalankan hanya unit test
php artisan test --testsuite=Unit

# Jalankan hanya feature/integration test
php artisan test --testsuite=Feature
```

---

## Strategi Pengujian

### Unit Testing (tests/Unit/)
Menguji logika bisnis murni tanpa database:

| File | Deskripsi |
|------|-----------|
| `BahanBakuObserverTest` | Konversi satuan (gram, kg, liter, ml, pcs, dll) |
| `KasirCartTest` | Logika keranjang belanja: tambah item, subtotal, total, hapus |
| `UserRoleTest` | Validasi role pengguna (pemilik vs kasir) |
| `StokPorsiCalculationTest` | Kalkulasi estimasi porsi dari stok bahan baku |

### Integration Testing (tests/Feature/)
Menguji interaksi dengan database (SQLite in-memory):

| File | Deskripsi |
|------|-----------|
| `BahanBakuTest` | CRUD bahan baku + relasi ke kategori |
| `ProdukTest` | CRUD produk + filter stok & ketersediaan |
| `TransaksiPenjualanTest` | Alur transaksi penjualan + kalkulasi total |
| `PembelianTest` | Alur pembelian bahan baku + relasi supplier |
| `AuthTest` | Autentikasi user + penyimpanan role |

### Test Coverage
Target minimal: **60% code coverage**

---

## Pipeline CI/CD (GitHub Actions)

Pipeline berjalan otomatis saat `push` atau `pull_request` ke branch `main`/`master`/`develop`.

Langkah pipeline:
1. Checkout kode
2. Setup PHP 8.2 + ekstensi yang dibutuhkan
3. Install dependencies via Composer
4. Generate app key
5. Jalankan semua test dengan laporan coverage (minimum 60%)
6. Upload laporan coverage sebagai artifact

Konfigurasi: `.github/workflows/ci.yml`
