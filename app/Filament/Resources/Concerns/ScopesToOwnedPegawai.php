<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ScopesToOwnedPegawai
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user?->hasRole('admin')) {
            return $query;
        }

        return $query->where('pegawai_id', $user?->pegawai?->id ?? 0);
    }
}
