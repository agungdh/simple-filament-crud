<?php

namespace App\Filament\Resources\PangkatGolongans\Pages;

use App\Filament\Resources\PangkatGolongans\PangkatGolonganResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePangkatGolongan extends CreateRecord
{
    protected static string $resource = PangkatGolonganResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
