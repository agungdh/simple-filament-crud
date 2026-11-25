<?php

namespace App\Filament\Resources\Bidangs\Pages;

use App\Filament\Resources\Bidangs\BidangResource;
use Filament\Resources\Pages\EditRecord;

class EditBidang extends EditRecord
{
    protected static string $resource = BidangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
