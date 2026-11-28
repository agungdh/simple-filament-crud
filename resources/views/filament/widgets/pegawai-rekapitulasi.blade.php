<x-filament-widgets::widget>
    <x-filament::section heading="Rekapitulasi Pegawai">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="px-3 py-2 text-left font-semibold">Triwulan I</th>
                        <th class="px-3 py-2 text-left font-semibold">Triwulan II</th>
                        <th class="px-3 py-2 text-left font-semibold">Triwulan III</th>
                        <th class="px-3 py-2 text-left font-semibold">Triwulan IV</th>
                        <th class="px-3 py-2 text-left font-semibold">Total JP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 dark:border-white/5">
                        <td class="px-3 py-2">{{ $triwulan[1] }}</td>
                        <td class="px-3 py-2">{{ $triwulan[2] }}</td>
                        <td class="px-3 py-2">{{ $triwulan[3] }}</td>
                        <td class="px-3 py-2">{{ $triwulan[4] }}</td>
                        <td class="px-3 py-2 font-semibold">{{ $total }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
