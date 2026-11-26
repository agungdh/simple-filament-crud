<?php

namespace App\Filament\Resources\PangkatGolongans\Pages;

use App\Filament\Resources\PangkatGolongans\PangkatGolonganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPangkatGolongans extends ListRecords
{
    protected static string $resource = PangkatGolonganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
