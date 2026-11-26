<?php

namespace App\Filament\Resources\Ppms\Pages;

use App\Filament\Resources\Ppms\PpmResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPpms extends ListRecords
{
    protected static string $resource = PpmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
