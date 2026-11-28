<?php

use Illuminate\Support\Facades\DB;

it('configures sqlite with the production pragmas', function () {
    $connection = DB::connection();

    expect(config('database.connections.sqlite.journal_mode'))->toBe('WAL')
        ->and(config('database.connections.sqlite.synchronous'))->toBe('NORMAL')
        ->and((int) $connection->selectOne('PRAGMA foreign_keys')->foreign_keys)->toBe(1)
        ->and((int) $connection->selectOne('PRAGMA busy_timeout')->timeout)->toBe(3000)
        ->and((int) $connection->selectOne('PRAGMA synchronous')->synchronous)->toBe(1)
        ->and((int) $connection->selectOne('PRAGMA temp_store')->temp_store)->toBe(2);
});
