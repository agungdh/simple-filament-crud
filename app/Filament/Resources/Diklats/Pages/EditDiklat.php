<?php

namespace App\Filament\Resources\Diklats\Pages;

use App\Filament\Resources\Diklats\DiklatResource;
use Filament\Resources\Pages\EditRecord;

class EditDiklat extends EditRecord
{
    protected static string $resource = DiklatResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
