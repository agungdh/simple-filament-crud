<?php

namespace App\Filament\Resources\Mentorings\Pages;

use App\Filament\Resources\Mentorings\MentoringResource;
use Filament\Resources\Pages\EditRecord;

class EditMentoring extends EditRecord
{
    protected static string $resource = MentoringResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
