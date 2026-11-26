<?php

namespace App\Filament\Resources\Lcs\Schemas;

use App\Filament\Resources\Concerns\KegiatanForm;
use Filament\Schemas\Schema;

class LcForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(KegiatanForm::components('lc'));
    }
}
