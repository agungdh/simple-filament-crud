<?php

namespace App\Filament\Resources\BelajarMandiris\Tables;

use App\Filament\Resources\Concerns\KegiatanTable;
use Filament\Tables\Table;

class BelajarMandirisTable
{
    public static function configure(Table $table): Table
    {
        return KegiatanTable::configure($table);
    }
}
