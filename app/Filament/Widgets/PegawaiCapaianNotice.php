<?php

namespace App\Filament\Widgets;

use App\Services\JamPelatihanService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class PegawaiCapaianNotice extends Widget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.pegawai-capaian-notice';

    public static function canView(): bool
    {
        return auth()->user()?->pegawai?->kategori_kebutuhan_jam_pelatihan !== null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $pegawai = auth()->user()?->pegawai;

        if ($pegawai === null) {
            return [];
        }

        $service = new JamPelatihanService($pegawai, $this->tahun());

        $capaian = $service->totalCapaian();
        $minimal = $service->jumlahMinimal();

        return [
            'tercapai' => $capaian >= $minimal,
            'capaian' => $capaian,
            'minimal' => $minimal,
        ];
    }

    private function tahun(): int
    {
        return (int) ($this->pageFilters['tahun'] ?? now()->year);
    }
}
