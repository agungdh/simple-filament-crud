<?php

namespace App\Filament\Resources\Concerns;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

final class BerkasAction
{
    public static function make(): Action
    {
        return Action::make('berkas')
            ->label('Berkas')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->visible(fn (Model $record): bool => filled($record->berkas))
            ->url(fn (Model $record): string => Storage::disk('local')->temporaryUrl($record->berkas, now()->addMinutes(5)))
            ->openUrlInNewTab();
    }
}
