<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PegawaiCapaianNotice;
use App\Filament\Widgets\PegawaiDetailPelatihan;
use App\Filament\Widgets\PegawaiRekapitulasi;
use App\Filament\Widgets\PegawaiStatsOverview;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    /**
     * Get the dashboard filter form.
     */
    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(self::tahunOptions())
                            ->default(now()->year)
                            ->selectablePlaceholder(false)
                            ->live(),
                        Select::make('modul')
                            ->label('Modul')
                            ->options(self::modulOptions())
                            ->default('')
                            ->selectablePlaceholder(false)
                            ->live(),
                    ])
                    ->columns(3),
            ]);
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            PegawaiStatsOverview::class,
            PegawaiCapaianNotice::class,
            PegawaiRekapitulasi::class,
            PegawaiDetailPelatihan::class,
        ];
    }

    public function getColumns(): int
    {
        return 1;
    }

    /**
     * Get the year options shown in the dashboard filter.
     *
     * @return array<int, string>
     */
    public static function tahunOptions(): array
    {
        return collect(range(now()->year, now()->year - 5))
            ->mapWithKeys(fn (int $tahun): array => [$tahun => (string) $tahun])
            ->all();
    }

    /**
     * Get the module options shown in the dashboard filter.
     *
     * @return array<string, string>
     */
    public static function modulOptions(): array
    {
        return [
            '' => 'Semua',
            'diklat' => 'Diklat',
            'ppm' => 'PPM',
            'seminar' => 'Seminar',
            'webinar' => 'Webinar',
            'lc' => 'LC',
        ];
    }
}
