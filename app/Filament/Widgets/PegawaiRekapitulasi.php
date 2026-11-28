<?php

namespace App\Filament\Widgets;

use App\Services\JamPelatihanService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class PegawaiRekapitulasi extends Widget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.pegawai-rekapitulasi';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $pegawai = auth()->user()?->pegawai;

        if ($pegawai === null) {
            return [
                'triwulan' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                'total' => 0,
            ];
        }

        $triwulan = (new JamPelatihanService($pegawai, $this->tahun()))->triwulan();

        return [
            'triwulan' => $triwulan,
            'total' => array_sum($triwulan),
        ];
    }

    private function tahun(): int
    {
        return (int) ($this->pageFilters['tahun'] ?? now()->year);
    }
}
