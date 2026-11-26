<?php

namespace App\Filament\Resources\MateriPpms\Pages;

use App\Filament\Resources\MateriPpms\MateriPpmResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMateriPpms extends ListRecords
{
    protected static string $resource = MateriPpmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
