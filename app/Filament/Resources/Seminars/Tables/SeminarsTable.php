<?php

namespace App\Filament\Resources\Seminars\Tables;

use App\Filament\Resources\Concerns\KegiatanTable;
use Filament\Tables\Table;

class SeminarsTable
{
    public static function configure(Table $table): Table
    {
        return KegiatanTable::configure($table);
    }
}
