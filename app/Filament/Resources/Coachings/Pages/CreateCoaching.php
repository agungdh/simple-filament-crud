<?php

namespace App\Filament\Resources\Coachings\Pages;

use App\Filament\Resources\Coachings\CoachingResource;
use App\Filament\Resources\Concerns\AssignsOwnedPegawai;
use Filament\Resources\Pages\CreateRecord;

class CreateCoaching extends CreateRecord
{
    use AssignsOwnedPegawai;

    protected static string $resource = CoachingResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
