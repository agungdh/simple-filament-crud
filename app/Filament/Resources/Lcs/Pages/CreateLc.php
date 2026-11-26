<?php

namespace App\Filament\Resources\Lcs\Pages;

use App\Filament\Resources\Concerns\AssignsOwnedPegawai;
use App\Filament\Resources\Lcs\LcResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLc extends CreateRecord
{
    use AssignsOwnedPegawai;

    protected static string $resource = LcResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
