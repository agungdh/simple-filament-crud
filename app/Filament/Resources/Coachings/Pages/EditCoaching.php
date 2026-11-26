<?php

namespace App\Filament\Resources\Coachings\Pages;

use App\Filament\Resources\Coachings\CoachingResource;
use Filament\Resources\Pages\EditRecord;

class EditCoaching extends EditRecord
{
    protected static string $resource = CoachingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
