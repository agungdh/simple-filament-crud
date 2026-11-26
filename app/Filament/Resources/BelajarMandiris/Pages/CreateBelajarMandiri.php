<?php

namespace App\Filament\Resources\BelajarMandiris\Pages;

use App\Filament\Resources\BelajarMandiris\BelajarMandiriResource;
use App\Filament\Resources\Concerns\AssignsOwnedPegawai;
use Filament\Resources\Pages\CreateRecord;

class CreateBelajarMandiri extends CreateRecord
{
    use AssignsOwnedPegawai;

    protected static string $resource = BelajarMandiriResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
