<?php

namespace App\Filament\Resources\Coachings\Tables;

use App\Filament\Resources\Concerns\KegiatanTable;
use Filament\Tables\Table;

class CoachingsTable
{
    public static function configure(Table $table): Table
    {
        return KegiatanTable::configure($table);
    }
}
