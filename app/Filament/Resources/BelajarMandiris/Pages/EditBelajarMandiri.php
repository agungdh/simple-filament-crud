<?php

namespace App\Filament\Resources\BelajarMandiris\Pages;

use App\Filament\Resources\BelajarMandiris\BelajarMandiriResource;
use Filament\Resources\Pages\EditRecord;

class EditBelajarMandiri extends EditRecord
{
    protected static string $resource = BelajarMandiriResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
