<?php

namespace App\Filament\Resources\Webinars\Tables;

use App\Filament\Resources\Concerns\KegiatanTable;
use Filament\Tables\Table;

class WebinarsTable
{
    public static function configure(Table $table): Table
    {
        return KegiatanTable::configure($table);
    }
}
