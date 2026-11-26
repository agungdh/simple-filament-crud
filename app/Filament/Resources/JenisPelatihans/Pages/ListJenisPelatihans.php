<?php

namespace App\Filament\Resources\JenisPelatihans\Pages;

use App\Filament\Resources\JenisPelatihans\JenisPelatihanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJenisPelatihans extends ListRecords
{
    protected static string $resource = JenisPelatihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
