<?php

namespace App\Filament\Resources\JenisPelatihans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JenisPelatihanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('jenis_pelatihan')
                    ->label('Jenis Pelatihan')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
