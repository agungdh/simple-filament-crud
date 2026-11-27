<?php

namespace App\Filament\Resources\Concerns;

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
use Illuminate\Database\Eloquent\Model;

final class KegiatanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->defaultSort('tanggal_pelaksanaan', 'desc')
            ->columns([
                TextColumn::make('pegawai.nama')
                    ->label('Pegawai')
                    ->description(fn (Model $record): ?string => $record->pegawai?->nip)
                    ->searchable()
                    ->sortable()
                    ->visible(fn (): bool => auth()->user()?->hasRole('admin')),
                TextColumn::make('materi_pengembangan')
                    ->label('Materi Pengembangan')
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('tanggal_pelaksanaan')
                    ->label('Tanggal Pelaksanaan')
                    ->date()
                    ->sortable(),
                TextColumn::make('jumlah_jam')
                    ->label('Jumlah Jam')
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
                    ->preload()
                    ->visible(fn (): bool => auth()->user()?->hasRole('admin')),
                Filter::make('tanggal_pelaksanaan')
                    ->schema([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['dari_tanggal'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('tanggal_pelaksanaan', '>=', $date),
                        )
                        ->when(
                            $data['sampai_tanggal'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('tanggal_pelaksanaan', '<=', $date),
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
