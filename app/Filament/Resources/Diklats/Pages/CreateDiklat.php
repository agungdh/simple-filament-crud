<?php

namespace App\Filament\Resources\Diklats\Pages;

use App\Filament\Resources\Diklats\DiklatResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiklat extends CreateRecord
{
    protected static string $resource = DiklatResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
