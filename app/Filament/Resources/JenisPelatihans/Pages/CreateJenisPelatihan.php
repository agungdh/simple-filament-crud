<?php

namespace App\Filament\Resources\JenisPelatihans\Pages;

use App\Filament\Resources\JenisPelatihans\JenisPelatihanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJenisPelatihan extends CreateRecord
{
    protected static string $resource = JenisPelatihanResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
