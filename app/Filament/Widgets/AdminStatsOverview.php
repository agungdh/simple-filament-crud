<?php

namespace App\Filament\Widgets;

use App\Enums\StatusPegawai;
use App\Models\Diklat;
use App\Models\Lc;
use App\Models\Pegawai;
use App\Models\Ppm;
use App\Models\Seminar;
use App\Models\Webinar;
use App\Services\JamPelatihanService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $tahun = $this->tahun();
        [$start, $end] = JamPelatihanService::yearRange($tahun);

        $aktif = fn ($query) => $query->where('status', StatusPegawai::Aktif);

        $diklat = Diklat::query()
            ->whereHas('pegawai', $aktif)
            ->whereBetween('dari_tanggal_pelaksanaan', [$start, $end])
            ->sum('jumlah_jam_pelatihan');

        $ppm = Ppm::query()
            ->whereHas('pegawai', $aktif)
            ->whereBetween('tanggal_pelaksanaan', [$start, $end])
            ->sum('jumlah_jam_pelatihan');

        $seminar = Seminar::query()
            ->whereHas('pegawai', $aktif)
            ->whereBetween('tanggal_pelaksanaan', [$start, $end])
            ->sum('jumlah_jam');

        $webinar = Webinar::query()
            ->whereHas('pegawai', $aktif)
            ->whereBetween('tanggal_pelaksanaan', [$start, $end])
            ->sum('jumlah_jam');

        $lc = Lc::query()
            ->whereHas('pegawai', $aktif)
            ->whereBetween('tanggal_pelaksanaan', [$start, $end])
            ->sum('jumlah_jam');

        return [
            Stat::make('Jumlah Pegawai', Pegawai::query()->where('status', StatusPegawai::Aktif)->count())
                ->color('primary'),
            Stat::make('Jumlah Jam Diklat', (int) $diklat)
                ->color('success'),
            Stat::make('Jumlah Jam PPM', (int) $ppm)
                ->color('warning'),
            Stat::make('Jumlah Jam Lain-Lain', (int) $seminar + (int) $webinar + (int) $lc)
                ->color('info'),
        ];
    }

    private function tahun(): int
    {
        return (int) ($this->pageFilters['tahun'] ?? now()->year);
    }
}
