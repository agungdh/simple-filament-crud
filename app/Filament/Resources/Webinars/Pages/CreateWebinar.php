<?php

namespace App\Filament\Resources\Webinars\Pages;

use App\Filament\Resources\Concerns\AssignsOwnedPegawai;
use App\Filament\Resources\Webinars\WebinarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWebinar extends CreateRecord
{
    use AssignsOwnedPegawai;

    protected static string $resource = WebinarResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
