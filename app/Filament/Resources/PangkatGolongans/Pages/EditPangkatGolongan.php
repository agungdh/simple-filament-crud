<?php

namespace App\Filament\Resources\PangkatGolongans\Pages;

use App\Filament\Resources\PangkatGolongans\PangkatGolonganResource;
use Filament\Resources\Pages\EditRecord;

class EditPangkatGolongan extends EditRecord
{
    protected static string $resource = PangkatGolonganResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
