<?php

namespace App\Filament\Resources\Webinars\Schemas;

use App\Filament\Resources\Concerns\KegiatanForm;
use Filament\Schemas\Schema;

class WebinarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(KegiatanForm::components('webinar'));
    }
}
