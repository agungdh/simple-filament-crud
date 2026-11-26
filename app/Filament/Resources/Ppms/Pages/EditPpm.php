<?php

namespace App\Filament\Resources\Ppms\Pages;

use App\Filament\Resources\Ppms\PpmResource;
use Filament\Resources\Pages\EditRecord;

class EditPpm extends EditRecord
{
    protected static string $resource = PpmResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
