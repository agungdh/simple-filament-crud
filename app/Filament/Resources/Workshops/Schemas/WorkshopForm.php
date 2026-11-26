<?php

namespace App\Filament\Resources\Workshops\Schemas;

use App\Filament\Resources\Concerns\KegiatanForm;
use Filament\Schemas\Schema;

class WorkshopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(KegiatanForm::components('workshop'));
    }
}
