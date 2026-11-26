<?php

namespace App\Filament\Resources\MateriPpms\Pages;

use App\Filament\Resources\MateriPpms\MateriPpmResource;
use Filament\Resources\Pages\EditRecord;

class EditMateriPpm extends EditRecord
{
    protected static string $resource = MateriPpmResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
