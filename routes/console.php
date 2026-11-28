<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Let SQLite refresh its query planner statistics periodically.
Schedule::call(fn () => DB::select('PRAGMA optimize'))
    ->daily()
    ->name('sqlite-optimize')
    ->withoutOverlapping();
