<?php

namespace App\Filament\Resources\Workshops\Pages;

use App\Filament\Resources\Workshops\WorkshopResource;
use Filament\Resources\Pages\EditRecord;

class EditWorkshop extends EditRecord
{
    protected static string $resource = WorkshopResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
