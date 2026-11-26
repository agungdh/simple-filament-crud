<?php

namespace App\Filament\Resources\JenisPelatihans\Pages;

use App\Filament\Resources\JenisPelatihans\JenisPelatihanResource;
use Filament\Resources\Pages\EditRecord;

class EditJenisPelatihan extends EditRecord
{
    protected static string $resource = JenisPelatihanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
