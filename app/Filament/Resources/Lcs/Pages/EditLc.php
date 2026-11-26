<?php

namespace App\Filament\Resources\Lcs\Pages;

use App\Filament\Resources\Lcs\LcResource;
use Filament\Resources\Pages\EditRecord;

class EditLc extends EditRecord
{
    protected static string $resource = LcResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
