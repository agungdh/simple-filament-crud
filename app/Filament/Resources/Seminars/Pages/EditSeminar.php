<?php

namespace App\Filament\Resources\Seminars\Pages;

use App\Filament\Resources\Seminars\SeminarResource;
use Filament\Resources\Pages\EditRecord;

class EditSeminar extends EditRecord
{
    protected static string $resource = SeminarResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
