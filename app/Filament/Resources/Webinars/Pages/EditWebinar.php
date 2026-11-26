<?php

namespace App\Filament\Resources\Webinars\Pages;

use App\Filament\Resources\Webinars\WebinarResource;
use Filament\Resources\Pages\EditRecord;

class EditWebinar extends EditRecord
{
    protected static string $resource = WebinarResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
