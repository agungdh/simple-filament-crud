<?php

namespace App\Filament\Resources\Mentorings\Tables;

use App\Filament\Resources\Concerns\KegiatanTable;
use Filament\Tables\Table;

class MentoringsTable
{
    public static function configure(Table $table): Table
    {
        return KegiatanTable::configure($table);
    }
}
