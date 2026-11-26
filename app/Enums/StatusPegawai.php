<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusPegawai: string implements HasColor, HasLabel
{
    case Aktif = 'aktif';
    case NonAktif = 'non aktif';

    public function getLabel(): string
    {
        return ucwords($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Aktif => 'success',
            self::NonAktif => 'gray',
        };
    }
}
