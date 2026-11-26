<?php

namespace App\Filament\Resources\Ppms\Schemas;

use App\Models\Pegawai;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PpmForm
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
                TextInput::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->required()
                    ->maxLength(255),
                Textarea::make('materi_pengembangan')
                    ->label('Materi Pengembangan')
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('tanggal_pelaksanaan')
                    ->label('Tanggal Pelaksanaan')
                    ->required(),
                TextInput::make('jumlah_jam_pelatihan')
                    ->label('Jumlah Jam Pelatihan')
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }
}
