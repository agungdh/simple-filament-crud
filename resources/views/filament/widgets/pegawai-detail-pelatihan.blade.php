<x-filament-widgets::widget>
    <x-filament::section heading="Detail Pegawai">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="px-3 py-2 text-left font-semibold">No</th>
                        <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                        <th class="px-3 py-2 text-left font-semibold">Tipe</th>
                        <th class="px-3 py-2 text-left font-semibold">Materi</th>
                        <th class="px-3 py-2 text-left font-semibold">Jumlah Jam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($detail as $index => $item)
                        <tr class="border-b border-gray-100 dark:border-white/5">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2">{{ $item['tanggal']?->format('d-m-Y') }}</td>
                            <td class="px-3 py-2">{{ $item['tipe'] }}</td>
                            <td class="px-3 py-2">{{ $item['materi'] }}</td>
                            <td class="px-3 py-2">{{ $item['jumlah_jam'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data pelatihan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="px-3 py-2 text-right font-semibold">Total Jam:</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ $total }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
