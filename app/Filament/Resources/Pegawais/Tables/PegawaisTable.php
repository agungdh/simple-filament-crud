<?php

namespace App\Filament\Resources\Pegawais\Tables;

use App\Enums\KategoriJabatan;
use App\Enums\KategoriKebutuhanJamPelatihan;
use App\Enums\StatusPegawai;
use App\Models\Pegawai;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PegawaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount(Pegawai::KEGIATAN_RELATIONS))
            ->columns([
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->state(fn (Pegawai $record): ?string => $record->user?->getRoleNames()->first())
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? '-'))
                    ->color(fn (?string $state): string => $state === 'admin' ? 'danger' : 'success'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('bidang.bidang')
                    ->label('Bidang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pangkat_golongan')
                    ->label('Pangkat Golongan')
                    ->state(fn (Pegawai $record): string => $record->pangkatGolongan?->label ?? '-'),
                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable(),
                TextColumn::make('kategori_jabatan')
                    ->label('Kategori Jabatan')
                    ->badge(),
                TextColumn::make('peran')
                    ->label('Peran')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('kategori_kebutuhan_jam_pelatihan')
                    ->label('Kategori JP')
                    ->badge()
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
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'pegawai' => 'Pegawai',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value']),
                        fn (Builder $query): Builder => $query->whereHas(
                            'user',
                            fn (Builder $query): Builder => $query->role($data['value']),
                        ),
                    )),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusPegawai::class),
                SelectFilter::make('bidang_id')
                    ->label('Bidang')
                    ->relationship('bidang', 'bidang')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('pangkat_golongan_id')
                    ->label('Pangkat Golongan')
                    ->relationship('pangkatGolongan', 'pangkat')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('kategori_jabatan')
                    ->label('Kategori Jabatan')
                    ->options(KategoriJabatan::class),
                SelectFilter::make('kategori_kebutuhan_jam_pelatihan')
                    ->label('Kategori Kebutuhan Jam Pelatihan')
                    ->options(KategoriKebutuhanJamPelatihan::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Pegawai $record): bool => $record->user_id === auth()->id() || $record->hasKegiatan())
                    ->tooltip(fn (Pegawai $record): ?string => match (true) {
                        $record->user_id === auth()->id() => 'Tidak dapat menghapus akun sendiri',
                        $record->hasKegiatan() => 'Pegawai masih memiliki data kegiatan',
                        default => null,
                    }),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Pegawai $record): bool => $record->user_id !== auth()->id() && ! $record->hasKegiatan(),
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
