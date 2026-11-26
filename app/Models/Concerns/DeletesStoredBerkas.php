<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait DeletesStoredBerkas
{
    protected static function bootDeletesStoredBerkas(): void
    {
        static::deleted(function (self $model): void {
            if (filled($model->berkas)) {
                Storage::disk('local')->delete($model->berkas);
            }
        });
    }
}
