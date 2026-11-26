<?php

namespace App\Filament\Resources\JenisPelatihans\Tables;

use App\Models\JenisPelatihan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JenisPelatihansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                TextColumn::make('jenis_pelatihan')
                    ->label('Jenis Pelatihan')
                    ->searchable()
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
                    ->disabled(fn (JenisPelatihan $record): bool => $record->diklats()->exists())
                    ->tooltip(fn (JenisPelatihan $record): ?string => $record->diklats()->exists() ? 'Jenis pelatihan masih dipakai diklat' : null),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (JenisPelatihan $record): bool => ! $record->diklats()->exists(),
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
