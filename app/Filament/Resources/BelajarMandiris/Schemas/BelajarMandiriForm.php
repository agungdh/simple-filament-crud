<?php

namespace App\Filament\Resources\BelajarMandiris\Schemas;

use App\Filament\Resources\Concerns\KegiatanForm;
use Filament\Schemas\Schema;

class BelajarMandiriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(KegiatanForm::components('belajar-mandiri'));
    }
}
