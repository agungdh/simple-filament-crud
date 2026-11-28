<?php

namespace App\Filament\Widgets;

use App\Services\JamPelatihanService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class PegawaiDetailPelatihan extends Widget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.pegawai-detail-pelatihan';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $pegawai = auth()->user()?->pegawai;

        if ($pegawai === null) {
            return [
                'detail' => collect(),
                'total' => 0,
            ];
        }

        $detail = (new JamPelatihanService($pegawai, $this->tahun()))->detail($this->modul());

        return [
            'detail' => $detail,
            'total' => $detail->sum('jumlah_jam'),
        ];
    }

    private function tahun(): int
    {
        return (int) ($this->pageFilters['tahun'] ?? now()->year);
    }

    private function modul(): ?string
    {
        $modul = $this->pageFilters['modul'] ?? null;

        return filled($modul) ? (string) $modul : null;
    }
}
