<?php

namespace App\Filament\Resources\Workshops\Tables;

use App\Filament\Resources\Concerns\KegiatanTable;
use Filament\Tables\Table;

class WorkshopsTable
{
    public static function configure(Table $table): Table
    {
        return KegiatanTable::configure($table);
    }
}
