<?php

namespace App\Filament\Resources\MateriPpms\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MateriPpmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->maxLength(255),
                TextInput::make('nama_pemateri')
                    ->label('Nama Pemateri')
                    ->maxLength(255),
                Textarea::make('materi_pengembangan')
                    ->label('Materi Pengembangan')
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('tanggal_pelaksanaan')
                    ->label('Tanggal Pelaksanaan')
                    ->required(),
                TextInput::make('link_materi')
                    ->label('Link Materi')
                    ->url()
                    ->maxLength(255),
                TextInput::make('link_dokumentasi')
                    ->label('Link Dokumentasi')
                    ->url()
                    ->maxLength(255),
            ]);
    }
}
