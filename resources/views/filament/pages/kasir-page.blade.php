<x-filament-panels::page>
<div class="flex gap-4 h-[80vh]">

    {{-- KIRI: Menu --}}
    <div class="flex-1 flex flex-col gap-3 overflow-hidden">

        {{-- Search --}}
        <input type="text" wire:model.live="search" placeholder="Cari menu..."
            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" />

        {{-- Kategori filter --}}
        <div class="flex gap-2 flex-wrap">
            <button wire:click="filterKategori(null)"
                style="{{ $kategori_id === null ? 'background:#f59e0b;color:white;' : 'background:#374151;color:#d1d5db;' }} padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;border:none;cursor:pointer;">
                Semua
            </button>
            @foreach($this->getKategoris() as $kat)
            <button wire:click="filterKategori({{ $kat->id }})"
                style="{{ $kategori_id === $kat->id ? 'background:#f59e0b;color:white;' : 'background:#374151;color:#d1d5db;' }} padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;border:none;cursor:pointer;">
                {{ $kat->nama }}
            </button>
            @endforeach
        </div>

        {{-- Grid produk --}}
        <div class="grid grid-cols-3 gap-3 overflow-y-auto pr-1">
            @forelse($this->getProduks() as $produk)
            <button wire:click="addToCart({{ $produk->id }})"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-3 text-left hover:border-amber-400 hover:shadow-md transition">
                @if($produk->gambar)
                <img src="{{ Storage::url($produk->gambar) }}" class="w-full h-24 object-cover rounded-lg mb-2" />
                @else
                <div class="w-full h-24 bg-gray-100 dark:bg-gray-700 rounded-lg mb-2 flex items-center justify-center text-gray-400 text-xs">No Image</div>
                @endif
                <p class="font-semibold text-sm truncate">{{ $produk->nama }}</p>
                <p class="text-amber-500 text-sm font-bold">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">Stok: {{ $produk->stok }}</p>
            </button>
            @empty
            <div class="col-span-3 text-center text-gray-400 py-8">Tidak ada produk tersedia</div>
            @endforelse
        </div>
    </div>

    {{-- KANAN: Order --}}
    <div class="w-80 flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <h2 class="font-bold text-lg mb-3">Order</h2>

        {{-- Cart items --}}
        <div class="flex-1 overflow-y-auto space-y-2">
            @forelse($cart as $produkId => $item)
            <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ $item['nama'] }}</p>
                    <p class="text-xs text-amber-500">Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                </div>
                <div class="flex items-center gap-1">
                    <button wire:click="updateJumlah({{ $produkId }}, {{ $item['jumlah'] - 1 }})"
                        class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 text-sm font-bold flex items-center justify-center hover:bg-red-200">-</button>
                    <span class="w-6 text-center text-sm font-semibold">{{ $item['jumlah'] }}</span>
                    <button wire:click="updateJumlah({{ $produkId }}, {{ $item['jumlah'] + 1 }})"
                        class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 text-sm font-bold flex items-center justify-center hover:bg-green-200">+</button>
                </div>
                <p class="text-xs font-semibold w-16 text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
            </div>
            @empty
            <div class="text-center text-gray-400 text-sm py-8">Belum ada pesanan</div>
            @endforelse
        </div>

        {{-- Total & actions --}}
        <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mt-3 space-y-3">
            <div class="flex justify-between font-bold text-lg">
                <span>Total</span>
                <span class="text-amber-500">Rp {{ number_format($this->getTotal(), 0, ',', '.') }}</span>
            </div>
            <button wire:click="simpanTransaksi"
                class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold text-sm">
                Simpan Pesanan
            </button>
            <button wire:click="clearCart"
                class="w-full py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 text-gray-700 dark:text-gray-300 rounded-lg text-sm">
                Batal
            </button>
        </div>
    </div>

</div>
</x-filament-panels::page>
