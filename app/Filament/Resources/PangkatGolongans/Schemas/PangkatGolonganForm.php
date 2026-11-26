<?php

namespace App\Filament\Resources\PangkatGolongans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PangkatGolonganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('jenjang')
                    ->label('Jenjang')
                    ->required()
                    ->maxLength(255),
                TextInput::make('pangkat')
                    ->label('Pangkat')
                    ->required()
                    ->maxLength(255),
                TextInput::make('golongan')
                    ->label('Golongan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('ruang')
                    ->label('Ruang')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
