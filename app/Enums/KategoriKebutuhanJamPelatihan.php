<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum KategoriKebutuhanJamPelatihan: string implements HasLabel
{
    case Admin = 'admin';
    case Pejabat = 'pejabat';
    case Auditor = 'auditor';

    public function getLabel(): string
    {
        return sprintf('%s (%d Jam)', ucwords($this->value), $this->jumlahMinimal());
    }

    public function jumlahMinimal(): int
    {
        return match ($this) {
            self::Admin => 20,
            self::Pejabat, self::Auditor => 40,
        };
    }
}
