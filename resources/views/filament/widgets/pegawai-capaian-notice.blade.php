@php
    $description = $tercapai
        ? "Anda telah mencapai jumlah minimum jam pelatihan. {$capaian} Jam Pelatihan dari {$minimal} Jam Pelatihan Minimal."
        : "Anda belum mencapai jumlah minimum jam pelatihan. {$capaian} Jam Pelatihan dari {$minimal} Jam Pelatihan Minimal.";
@endphp

<x-filament-widgets::widget>
    <x-filament::callout
        :color="$tercapai ? 'success' : 'warning'"
        :icon="$tercapai ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'"
        :heading="$tercapai ? 'Selamat!' : 'Perhatian!'"
        :description="$description"
    />
</x-filament-widgets::widget>
