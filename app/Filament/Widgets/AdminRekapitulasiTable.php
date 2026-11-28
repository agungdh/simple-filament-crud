<?php

namespace App\Filament\Widgets;

use App\Enums\StatusPegawai;
use App\Models\Pegawai;
use App\Services\JamPelatihanService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class AdminRekapitulasiTable extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected static ?string $heading = 'Rekapitulasi Pegawai';

    /**
     * @var array<int, array<int, int>>
     */
    private array $triwulanCache = [];

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->getRekapitulasiQuery())
            ->defaultSort('nip')
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('triwulan_1')
                    ->label('Triwulan I')
                    ->alignCenter()
                    ->state(fn (Pegawai $record): int => $this->triwulanFor($record)[1]),
                TextColumn::make('triwulan_2')
                    ->label('Triwulan II')
                    ->alignCenter()
                    ->state(fn (Pegawai $record): int => $this->triwulanFor($record)[2]),
                TextColumn::make('triwulan_3')
                    ->label('Triwulan III')
                    ->alignCenter()
                    ->state(fn (Pegawai $record): int => $this->triwulanFor($record)[3]),
                TextColumn::make('triwulan_4')
                    ->label('Triwulan IV')
                    ->alignCenter()
                    ->state(fn (Pegawai $record): int => $this->triwulanFor($record)[4]),
                TextColumn::make('total_jp')
                    ->label('Total JP')
                    ->alignCenter()
                    ->weight('bold')
                    ->state(fn (Pegawai $record): int => array_sum($this->triwulanFor($record))),
            ]);
    }

    private function getRekapitulasiQuery(): Builder
    {
        return Pegawai::query()
            ->where('status', StatusPegawai::Aktif)
            ->with(JamPelatihanService::yearConstraints($this->tahun()));
    }

    /**
     * @return array<int, int>
     */
    private function triwulanFor(Pegawai $record): array
    {
        return $this->triwulanCache[$record->getKey()] ??= JamPelatihanService::triwulanFromRelations($record, $this->modul());
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
