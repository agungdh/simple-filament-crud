<?php

namespace App\Filament\Resources\Seminars\Schemas;

use App\Filament\Resources\Concerns\KegiatanForm;
use Filament\Schemas\Schema;

class SeminarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(KegiatanForm::components('seminar'));
    }
}
