<?php

namespace App\Filament\Resources\BelajarMandiris\Pages;

use App\Filament\Resources\BelajarMandiris\BelajarMandiriResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBelajarMandiris extends ListRecords
{
    protected static string $resource = BelajarMandiriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
