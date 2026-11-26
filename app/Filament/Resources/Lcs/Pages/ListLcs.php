<?php

namespace App\Filament\Resources\Lcs\Pages;

use App\Filament\Resources\Lcs\LcResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLcs extends ListRecords
{
    protected static string $resource = LcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
