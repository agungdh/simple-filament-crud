<?php

namespace App\Filament\Resources\Diklats\Tables;

use App\Filament\Resources\Concerns\BerkasAction;
use App\Models\Pegawai;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DiklatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->defaultSort('dari_tanggal_pelaksanaan', 'desc')
            ->columns([
                TextColumn::make('pegawai.nama')
                    ->label('Pegawai')
                    ->description(fn ($record): ?string => $record->pegawai?->nip)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_pelatihan.jenis_pelatihan')
                    ->label('Jenis Pelatihan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('materi_pengembangan')
                    ->label('Materi Pengembangan')
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('dari_tanggal_pelaksanaan')
                    ->label('Dari Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('sampai_tanggal_pelaksanaan')
                    ->label('Sampai Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('jumlah_jam_pelatihan')
                    ->label('Jumlah Jam Pelatihan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('filename')
                    ->label('Berkas')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('pegawai_id')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'nama')
                    ->getOptionLabelFromRecordUsing(fn (Pegawai $record): string => "{$record->nama} - {$record->nip}")
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jenis_pelatihan_id')
                    ->label('Jenis Pelatihan')
                    ->relationship('jenisPelatihan', 'jenis_pelatihan')
                    ->searchable()
                    ->preload(),
                Filter::make('tanggal_pelaksanaan')
                    ->schema([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['dari_tanggal'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('sampai_tanggal_pelaksanaan', '>=', $date),
                        )
                        ->when(
                            $data['sampai_tanggal'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('dari_tanggal_pelaksanaan', '<=', $date),
                        )),
            ])
            ->recordActions([
                BerkasAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
