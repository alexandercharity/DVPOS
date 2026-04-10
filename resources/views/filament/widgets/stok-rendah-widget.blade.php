<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">⚠️ Bahan Baku Stok Rendah (≤ 5)</x-slot>
        @php $items = $this->getStokRendah(); @endphp
        @if($items->isEmpty())
            <p class="text-sm text-gray-400">Semua bahan baku stok aman.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 text-left">Bahan Baku</th>
                            <th class="py-2 text-left">Satuan</th>
                            <th class="py-2 text-center">Stok</th>
                            <th class="py-2 text-center">Min. Stok</th>
                            <th class="py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 font-medium">{{ $item->nama }}</td>
                            <td class="py-2 text-gray-500">{{ $item->satuan }}</td>
                            <td class="py-2 text-center font-bold {{ $item->stok == 0 ? 'text-red-500' : 'text-yellow-500' }}">
                                {{ $item->stok + 0 }}
                            </td>
                            <td class="py-2 text-gray-500 text-center">{{ $item->stok_minimum + 0 }}</td>
                            <td class="py-2">
                                <span style="{{ $item->stok == 0 ? 'background:#ef4444;color:white;' : 'background:#facc15;color:#1f2937;' }} padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">
                                    {{ $item->stok == 0 ? 'Habis' : 'Hampir Habis' }}
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
