<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\Permission\Models\Role;

#[Signature('legacy:import {path : Path to the legacy MySQL dump (.sql or .sql.gz)} {--fresh : Truncate the target tables before importing} {--force : Import even when the target tables already contain data} {--dry-run : Parse the dump and report row counts without writing to the database}')]
#[Description('Import data from the legacy ate_bpkp_laporan MySQL dump')]
class ImportLegacyData extends Command
{
    /**
     * Tables imported from the dump, in foreign-key-safe order.
     *
     * @var list<string>
     */
    private const TABLES = [
        'bidangs',
        'pangkat_golongans',
        'jenis_pelatihans',
        'users',
        'pegawais',
        'ppms',
        'diklats',
        'seminars',
        'webinars',
        'lcs',
        'belajar_mandiris',
        'mentorings',
        'coachings',
        'workshops',
        'materi_ppms',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->resolvePath($this->argument('path'));

        if (! is_file($path)) {
            $this->components->error("Dump file not found: {$path}");

            return self::FAILURE;
        }

        $tables = $this->parseInserts($this->readDump($path));

        $missing = array_values(array_diff(self::TABLES, array_keys($tables)));

        if ($missing !== []) {
            $this->components->warn('Tables not found in dump: '.implode(', ', $missing));
        }

        if ($this->option('dry-run')) {
            $this->renderSummary($tables);

            return self::SUCCESS;
        }

        if (! $this->option('fresh') && ! $this->option('force') && $this->hasExistingData()) {
            $this->components->error('Target tables already contain data. Re-run with --fresh to replace it.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($tables): void {
            DB::statement("set local time zone 'UTC'");

            if ($this->option('fresh')) {
                $this->truncateTargetTables();
            }

            $this->importTables($tables);
            $this->importRoles($tables);
            $this->resetSequences();
        });

        $this->renderSummary($tables, imported: true);

        return self::SUCCESS;
    }

    /**
     * Resolve the dump path relative to the application root.
     */
    private function resolvePath(string $path): string
    {
        if (str_starts_with($path, '/')) {
            return $path;
        }

        return base_path($path);
    }

    /**
     * Read a plain or gzip-compressed dump into a single string.
     */
    private function readDump(string $path): string
    {
        if (str_ends_with($path, '.gz')) {
            $lines = gzfile($path);

            if ($lines === false) {
                throw new RuntimeException("Unable to read dump: {$path}");
            }

            return implode('', $lines);
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read dump: {$path}");
        }

        return $contents;
    }

    /**
     * Extract every INSERT statement from the dump.
     *
     * @return array<string, list<array<string, string|null>>>
     */
    private function parseInserts(string $sql): array
    {
        $tables = [];
        $offset = 0;

        while (preg_match('/INSERT INTO `([^`]+)` \(([^)]*)\) VALUES\s*/', $sql, $match, PREG_OFFSET_CAPTURE, $offset) === 1) {
            $table = $match[1][0];
            $columns = array_map(
                static fn (string $column): string => trim($column, " `\n\r\t"),
                explode(',', $match[2][0]),
            );

            $start = $match[0][1] + strlen($match[0][0]);
            [$values, $offset] = $this->readStatement($sql, $start);

            foreach ($this->parseRows($values) as $row) {
                $tables[$table][] = array_combine($columns, $row);
            }
        }

        return $tables;
    }

    /**
     * Read a VALUES clause up to the terminating semicolon.
     *
     * @return array{0: string, 1: int}
     */
    private function readStatement(string $sql, int $offset): array
    {
        $length = strlen($sql);
        $inString = false;

        for ($position = $offset; $position < $length; $position++) {
            $character = $sql[$position];

            if ($inString) {
                if ($character === '\\') {
                    $position++;

                    continue;
                }

                if ($character === "'") {
                    $inString = false;
                }

                continue;
            }

            if ($character === "'") {
                $inString = true;

                continue;
            }

            if ($character === ';') {
                return [substr($sql, $offset, $position - $offset), $position + 1];
            }
        }

        return [substr($sql, $offset), $length];
    }

    /**
     * Split a VALUES clause into rows of scalar values.
     *
     * @return list<list<string|null>>
     */
    private function parseRows(string $values): array
    {
        $rows = [];
        $row = [];
        $value = '';
        $hasValue = false;
        $quoted = false;
        $inString = false;
        $length = strlen($values);

        for ($position = 0; $position < $length; $position++) {
            $character = $values[$position];

            if ($inString) {
                if ($character === '\\') {
                    $value .= $this->unescape($values[$position + 1] ?? '');
                    $position++;

                    continue;
                }

                if ($character === "'") {
                    if (($values[$position + 1] ?? '') === "'") {
                        $value .= "'";
                        $position++;

                        continue;
                    }

                    $inString = false;

                    continue;
                }

                $value .= $character;

                continue;
            }

            if ($character === "'") {
                $inString = true;
                $hasValue = true;
                $quoted = true;

                continue;
            }

            if ($character === '(') {
                $row = [];
                $value = '';
                $hasValue = false;
                $quoted = false;

                continue;
            }

            if ($character === ',' || $character === ')') {
                $row[] = $this->normalizeValue($hasValue ? $value : null, $quoted);

                $value = '';
                $hasValue = false;
                $quoted = false;

                if ($character === ')') {
                    $rows[] = $row;
                    $row = [];
                }

                continue;
            }

            if (ctype_space($character)) {
                continue;
            }

            $value .= $character;
            $hasValue = true;
        }

        return $rows;
    }

    /**
     * Convert an unquoted NULL literal into a real null.
     */
    private function normalizeValue(?string $value, bool $quoted): ?string
    {
        if (! $quoted && $value === 'NULL') {
            return null;
        }

        return $value;
    }

    /**
     * Resolve a MySQL backslash escape sequence.
     */
    private function unescape(string $character): string
    {
        return match ($character) {
            '0' => "\0",
            'b' => "\x08",
            'n' => "\n",
            'r' => "\r",
            't' => "\t",
            'Z' => "\x1a",
            default => $character,
        };
    }

    /**
     * Determine whether any target table already contains rows.
     */
    private function hasExistingData(): bool
    {
        foreach (self::TABLES as $table) {
            if (DB::table($table)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Empty the target tables and reset their identities.
     */
    private function truncateTargetTables(): void
    {
        $tables = array_map(
            static fn (string $table): string => '"'.$table.'"',
            [...self::TABLES, 'model_has_roles'],
        );

        DB::statement('truncate table '.implode(', ', $tables).' restart identity cascade');
    }

    /**
     * Insert the parsed rows into their target tables.
     *
     * @param  array<string, list<array<string, string|null>>>  $tables
     */
    private function importTables(array $tables): void
    {
        foreach (self::TABLES as $table) {
            $rows = $tables[$table] ?? [];

            if ($rows === []) {
                continue;
            }

            $columns = array_values(array_intersect(
                Schema::getColumnListing($table),
                array_keys($rows[0]),
            ));

            if ($columns === []) {
                continue;
            }

            $columnLookup = array_flip($columns);

            $prepared = array_map(
                static fn (array $row): array => array_intersect_key($row, $columnLookup),
                $rows,
            );

            foreach (array_chunk($prepared, 500) as $chunk) {
                DB::table($table)->insert($chunk);
            }
        }
    }

    /**
     * Assign the legacy Spatie roles to the imported users.
     *
     * @param  array<string, list<array<string, string|null>>>  $tables
     */
    private function importRoles(array $tables): void
    {
        $roleNames = [];

        foreach ($tables['roles'] ?? [] as $role) {
            $roleNames[(int) $role['id']] = $role['name'];
            Role::findOrCreate($role['name'], 'web');
        }

        $assignments = [];

        foreach ($tables['model_has_roles'] ?? [] as $assignment) {
            $roleName = $roleNames[(int) $assignment['role_id']] ?? null;

            if ($roleName === null || $assignment['model_type'] !== User::class) {
                continue;
            }

            $assignments[(int) $assignment['model_id']][] = $roleName;
        }

        if ($assignments === []) {
            return;
        }

        $users = User::query()
            ->whereKey(array_keys($assignments))
            ->get()
            ->keyBy('id');

        foreach ($assignments as $userId => $roles) {
            $users->get($userId)?->syncRoles($roles);
        }
    }

    /**
     * Point every identity sequence at the highest imported id.
     */
    private function resetSequences(): void
    {
        foreach (self::TABLES as $table) {
            $quoted = '"'.$table.'"';

            DB::statement(
                'select setval(pg_get_serial_sequence(?, \'id\'), coalesce((select max(id) from '.$quoted.'), 1), (select count(*) > 0 from '.$quoted.'))',
                [$table],
            );
        }
    }

    /**
     * Render the per-table row counts.
     *
     * @param  array<string, list<array<string, string|null>>>  $tables
     */
    private function renderSummary(array $tables, bool $imported = false): void
    {
        $rows = [];

        foreach (self::TABLES as $table) {
            $rows[] = [
                $table,
                count($tables[$table] ?? []),
                $imported ? DB::table($table)->count() : '—',
            ];
        }

        $this->table(['Table', 'In dump', 'In database'], $rows);
    }
}
