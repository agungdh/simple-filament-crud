<?php

namespace App\Filament\Resources\Pegawais\Schemas;

use App\Enums\KategoriJabatan;
use App\Enums\KategoriKebutuhanJamPelatihan;
use App\Enums\StatusPegawai;
use App\Models\PangkatGolongan;
use App\Models\Pegawai;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nip')
                    ->label('NIP')
                    ->required()
                    ->tel()
                    ->rule('digits_between:1,18')
                    ->unique(ignoreRecord: true)
                    ->unique(
                        table: User::class,
                        column: 'username',
                        ignorable: fn (?Pegawai $record): ?User => $record?->user,
                    ),
                TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'pegawai' => 'Pegawai',
                    ])
                    ->default('pegawai')
                    ->required()
                    ->formatStateUsing(
                        fn (?Pegawai $record): string => $record?->user?->getRoleNames()->first() ?? 'pegawai',
                    ),
                Select::make('status')
                    ->label('Status')
                    ->options(StatusPegawai::class)
                    ->default(StatusPegawai::Aktif)
                    ->required(),
                Select::make('bidang_id')
                    ->label('Bidang')
                    ->relationship('bidang', 'bidang')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('pangkat_golongan_id')
                    ->label('Pangkat Golongan')
                    ->relationship('pangkatGolongan', 'pangkat')
                    ->getOptionLabelFromRecordUsing(
                        fn (PangkatGolongan $record): string => $record->label,
                    )
                    ->searchable()
                    ->preload(),
                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->maxLength(255),
                TextInput::make('peran')
                    ->label('Peran')
                    ->maxLength(255),
                Select::make('kategori_jabatan')
                    ->label('Kategori Jabatan')
                    ->options(KategoriJabatan::class),
                Select::make('kategori_kebutuhan_jam_pelatihan')
                    ->label('Kategori Kebutuhan Jam Pelatihan')
                    ->options(KategoriKebutuhanJamPelatihan::class),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->rule('confirmed')
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(false),
            ]);
    }
}
