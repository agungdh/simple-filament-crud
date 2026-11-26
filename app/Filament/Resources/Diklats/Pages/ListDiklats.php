<?php

namespace App\Filament\Resources\Diklats\Pages;

use App\Filament\Resources\Diklats\DiklatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiklats extends ListRecords
{
    protected static string $resource = DiklatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
