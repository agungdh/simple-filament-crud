<?php

namespace App\Filament\Resources\PangkatGolongans\Tables;

use App\Models\PangkatGolongan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PangkatGolongansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                TextColumn::make('jenjang')
                    ->label('Jenjang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pangkat')
                    ->label('Pangkat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('golongan')
                    ->label('Golongan')
                    ->sortable(),
                TextColumn::make('ruang')
                    ->label('Ruang')
                    ->sortable(),
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
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (PangkatGolongan $record): bool => $record->pegawais()->exists())
                    ->tooltip(fn (PangkatGolongan $record): ?string => $record->pegawais()->exists() ? 'Pangkat golongan masih dipakai pegawai' : null),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (PangkatGolongan $record): bool => ! $record->pegawais()->exists(),
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
