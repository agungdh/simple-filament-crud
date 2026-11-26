<?php

namespace App\Filament\Resources\MateriPpms\Tables;

use App\Models\MateriPpm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MateriPpmsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->defaultSort('tanggal_pelaksanaan', 'desc')
            ->columns([
                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_pemateri')
                    ->label('Nama Pemateri')
                    ->searchable(),
                TextColumn::make('materi_pengembangan')
                    ->label('Materi Pengembangan')
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('tanggal_pelaksanaan')
                    ->label('Tanggal Pelaksanaan')
                    ->date()
                    ->sortable(),
                TextColumn::make('link_materi')
                    ->label('Link Materi')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (MateriPpm $record): ?string => $record->link_materi)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Buka Materi' : '-')
                    ->toggleable(),
                TextColumn::make('link_dokumentasi')
                    ->label('Link Dokumentasi')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (MateriPpm $record): ?string => $record->link_dokumentasi)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Buka Dokumentasi' : '-')
                    ->toggleable(),
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
