<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Produk Stok Rendah (≤ 5)</x-slot>
        @php $produk = $this->getStokRendah(); @endphp
        @if($produk->isEmpty())
            <p class="text-sm text-gray-400">Semua produk stok aman.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 text-left">Produk</th>
                            <th class="py-2 text-left">Kategori</th>
                            <th class="py-2 text-center">Stok</th>
                            <th class="py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk as $p)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2">{{ $p->nama }}</td>
                            <td class="py-2 text-gray-500">{{ $p->kategori->nama ?? '-' }}</td>
                            <td class="py-2 text-center font-bold {{ $p->stok == 0 ? 'text-red-500' : 'text-yellow-500' }}">
                                {{ $p->stok }}
                            </td>
                            <td class="py-2">
                                <span style="{{ $p->stok == 0 ? 'background:#ef4444;color:white;' : 'background:#facc15;color:#1f2937;' }} padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">
                                    {{ $p->stok == 0 ? 'Habis' : 'Hampir Habis' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
