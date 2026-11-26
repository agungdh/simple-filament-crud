<?php

namespace App\Filament\Resources\Ppms\Pages;

use App\Filament\Resources\Ppms\PpmResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePpm extends CreateRecord
{
    protected static string $resource = PpmResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
