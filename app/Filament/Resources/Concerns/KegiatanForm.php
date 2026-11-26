<?php

namespace App\Filament\Resources\Concerns;

use App\Models\Pegawai;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

final class KegiatanForm
{
    /**
     * @return array<int, Component>
     */
    public static function components(string $directory): array
    {
        return [
            Select::make('pegawai_id')
                ->label('Pegawai')
                ->relationship('pegawai', 'nama')
                ->getOptionLabelFromRecordUsing(fn (Pegawai $record): string => "{$record->nama} - {$record->nip}")
                ->searchable(['nama', 'nip'])
                ->preload()
                ->required()
                ->visible(fn (): bool => auth()->user()?->hasRole('admin')),
            Textarea::make('materi_pengembangan')
                ->label('Materi Pengembangan')
                ->required()
                ->columnSpanFull(),
            DatePicker::make('tanggal_pelaksanaan')
                ->label('Tanggal Pelaksanaan')
                ->required(),
            TextInput::make('jumlah_jam')
                ->label('Jumlah Jam')
                ->required()
                ->numeric()
                ->minValue(1),
            FileUpload::make('berkas')
                ->label('Berkas')
                ->disk('local')
                ->directory($directory)
                ->storeFileNamesIn('filename')
                ->maxSize(10240)
                ->columnSpanFull(),
        ];
    }
}
