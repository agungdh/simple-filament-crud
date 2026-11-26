<?php

namespace App\Filament\Resources\MateriPpms\Pages;

use App\Filament\Resources\MateriPpms\MateriPpmResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMateriPpm extends CreateRecord
{
    protected static string $resource = MateriPpmResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
