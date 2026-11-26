<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum KategoriJabatan: string implements HasLabel
{
    case Struktural = 'struktural';
    case FungsionalAuditor = 'fungsional auditor';
    case FungsionalTertentu = 'fungsional tertentu';

    public function getLabel(): string
    {
        return ucwords($this->value);
    }
}
