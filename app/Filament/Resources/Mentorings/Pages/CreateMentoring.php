<?php

namespace App\Filament\Resources\Mentorings\Pages;

use App\Filament\Resources\Concerns\AssignsOwnedPegawai;
use App\Filament\Resources\Mentorings\MentoringResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMentoring extends CreateRecord
{
    use AssignsOwnedPegawai;

    protected static string $resource = MentoringResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
