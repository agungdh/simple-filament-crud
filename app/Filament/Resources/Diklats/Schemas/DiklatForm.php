<?php

namespace App\Filament\Resources\Diklats\Schemas;

use App\Models\Pegawai;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DiklatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pegawai_id')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'nama')
                    ->getOptionLabelFromRecordUsing(fn (Pegawai $record): string => "{$record->nama} - {$record->nip}")
                    ->searchable(['nama', 'nip'])
                    ->preload()
                    ->required(),
                Select::make('jenis_pelatihan_id')
                    ->label('Jenis Pelatihan')
                    ->relationship('jenisPelatihan', 'jenis_pelatihan')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->required()
                    ->maxLength(255),
                Textarea::make('materi_pengembangan')
                    ->label('Materi Pengembangan')
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('dari_tanggal_pelaksanaan')
                    ->label('Dari Tanggal Pelaksanaan')
                    ->required()
                    ->maxDate(fn (Get $get): mixed => $get('sampai_tanggal_pelaksanaan'))
                    ->beforeOrEqual('sampai_tanggal_pelaksanaan'),
                DatePicker::make('sampai_tanggal_pelaksanaan')
                    ->label('Sampai Tanggal Pelaksanaan')
                    ->required()
                    ->minDate(fn (Get $get): mixed => $get('dari_tanggal_pelaksanaan'))
                    ->afterOrEqual('dari_tanggal_pelaksanaan'),
                TextInput::make('jumlah_jam_pelatihan')
                    ->label('Jumlah Jam Pelatihan')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                FileUpload::make('berkas')
                    ->label('Berkas')
                    ->disk('local')
                    ->directory('diklat')
                    ->storeFileNamesIn('filename')
                    ->maxSize(10240)
                    ->columnSpanFull(),
            ]);
    }
}
