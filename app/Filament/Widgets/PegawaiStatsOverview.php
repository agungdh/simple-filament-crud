<?php

namespace App\Filament\Widgets;

use App\Services\JamPelatihanService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PegawaiStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $pegawai = auth()->user()?->pegawai;

        if ($pegawai === null) {
            return [];
        }

        $capaian = (new JamPelatihanService($pegawai, $this->tahun()))->capaian();

        return [
            Stat::make('Jumlah Jam Diklat', $capaian['diklat'])
                ->color('success'),
            Stat::make('Jumlah Jam PPM', $capaian['ppm'])
                ->color('warning'),
            Stat::make('Jumlah Jam Lain-Lain', $capaian['seminar'] + $capaian['webinar'] + $capaian['lc'])
                ->color('info'),
        ];
    }

    private function tahun(): int
    {
        return (int) ($this->pageFilters['tahun'] ?? now()->year);
    }
}
