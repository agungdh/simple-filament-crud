<?php

namespace App\Services;

use App\Models\Pegawai;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class JamPelatihanService
{
    /**
     * @var array<string, array{label: string, relation: string, date: string, jam: string}>
     */
    public const MODULES = [
        'diklat' => [
            'label' => 'Diklat',
            'relation' => 'diklats',
            'date' => 'dari_tanggal_pelaksanaan',
            'jam' => 'jumlah_jam_pelatihan',
        ],
        'ppm' => [
            'label' => 'PPM',
            'relation' => 'ppms',
            'date' => 'tanggal_pelaksanaan',
            'jam' => 'jumlah_jam_pelatihan',
        ],
        'seminar' => [
            'label' => 'Seminar',
            'relation' => 'seminars',
            'date' => 'tanggal_pelaksanaan',
            'jam' => 'jumlah_jam',
        ],
        'webinar' => [
            'label' => 'Webinar',
            'relation' => 'webinars',
            'date' => 'tanggal_pelaksanaan',
            'jam' => 'jumlah_jam',
        ],
        'lc' => [
            'label' => 'LC',
            'relation' => 'lcs',
            'date' => 'tanggal_pelaksanaan',
            'jam' => 'jumlah_jam',
        ],
    ];

    public function __construct(
        public Pegawai $pegawai,
        public int $tahun,
    ) {}

    /**
     * Get the first and last day of the given year.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function yearRange(int $tahun): array
    {
        return [
            Carbon::create($tahun, 1, 1)->startOfDay(),
            Carbon::create($tahun, 12, 31)->endOfDay(),
        ];
    }

    /**
     * Get eager load constraints that limit each kegiatan relation to the given year.
     *
     * @return array<string, Closure>
     */
    public static function yearConstraints(int $tahun): array
    {
        [$start, $end] = self::yearRange($tahun);

        $constraints = [];

        foreach (self::MODULES as $module) {
            $constraints[$module['relation']] = fn ($query) => $query->whereBetween($module['date'], [$start, $end]);
        }

        return $constraints;
    }

    public function jumlahMinimal(): int
    {
        return $this->pegawai->kategori_kebutuhan_jam_pelatihan?->jumlahMinimal() ?? 0;
    }

    /**
     * Get the total jam for each module.
     *
     * @return array<string, int>
     */
    public function capaian(): array
    {
        $this->loadKegiatan();

        $capaian = [];

        foreach (self::MODULES as $key => $module) {
            $capaian[$key] = (int) $this->pegawai->{$module['relation']}->sum($module['jam']);
        }

        return $capaian;
    }

    public function totalCapaian(): int
    {
        return array_sum($this->capaian());
    }

    /**
     * Get the total jam for each quarter.
     *
     * @return array<int, int>
     */
    public function triwulan(?string $module = null): array
    {
        $this->loadKegiatan();

        return self::triwulanFromRelations($this->pegawai, $module);
    }

    /**
     * Get the quarterly totals from relations that are already loaded.
     *
     * @return array<int, int>
     */
    public static function triwulanFromRelations(Pegawai $pegawai, ?string $module = null): array
    {
        $totals = [1 => 0, 2 => 0, 3 => 0, 4 => 0];

        foreach (self::modulesFor($module) as $definition) {
            foreach ($pegawai->{$definition['relation']} as $record) {
                $totals[self::quarterOf($record->{$definition['date']})] += (int) $record->{$definition['jam']};
            }
        }

        return $totals;
    }

    /**
     * Get every kegiatan record in the given year, ordered by date.
     *
     * @return Collection<int, array{tanggal: Carbon|null, tipe: string, materi: string, jumlah_jam: int}>
     */
    public function detail(?string $module = null): Collection
    {
        $this->loadKegiatan();

        $detail = collect();

        foreach (self::modulesFor($module) as $definition) {
            foreach ($this->pegawai->{$definition['relation']} as $record) {
                $detail->push([
                    'tanggal' => $record->{$definition['date']},
                    'tipe' => $definition['label'],
                    'materi' => $record->materi_pengembangan,
                    'jumlah_jam' => (int) $record->{$definition['jam']},
                ]);
            }
        }

        return $detail
            ->sortBy(fn (array $item): int => $item['tanggal']?->getTimestamp() ?? 0)
            ->values();
    }

    /**
     * @return array<string, array{label: string, relation: string, date: string, jam: string}>
     */
    private static function modulesFor(?string $module): array
    {
        if (($module !== null) && array_key_exists($module, self::MODULES)) {
            return [$module => self::MODULES[$module]];
        }

        return self::MODULES;
    }

    private function loadKegiatan(): void
    {
        $this->pegawai->load(self::yearConstraints($this->tahun));
    }

    private static function quarterOf(?Carbon $tanggal): int
    {
        return intdiv(($tanggal?->month ?? 1) - 1, 3) + 1;
    }
}
