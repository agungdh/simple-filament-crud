<?php

namespace App\Filament\Resources\Seminars\Pages;

use App\Filament\Resources\Concerns\AssignsOwnedPegawai;
use App\Filament\Resources\Seminars\SeminarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSeminar extends CreateRecord
{
    use AssignsOwnedPegawai;

    protected static string $resource = SeminarResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
