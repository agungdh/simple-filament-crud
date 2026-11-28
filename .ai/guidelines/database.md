# SQLite

This project runs on SQLite only: `DB_CONNECTION=sqlite` with the database file at `database/database.sqlite`. Tests run against an in-memory SQLite database (`:memory:`). Do not write PostgreSQL/MySQL-only SQL or rely on their behaviour.

## Connection and raw SQL

- Keep `DB_CONNECTION=sqlite` in `.env`, `.env.example`, and CI. `DB_DATABASE` is optional and falls back to `database_path('database.sqlite')`.
- Prefer the schema builder and Eloquent. When raw SQL is unavoidable, keep it SQLite-valid, parameterised, and pass bindings to `DB::select()`/`DB::statement()` instead of interpolating values.
- Do not add PostgreSQL/MySQL fallbacks to make a query pass.
- The database is a single file; include it in backups and never commit it.

## Timestamps

- SQLite stores instants as text. Keep using `timestampsTz()`, `timestampTz()`, `dateTimeTz()`, `timeTz()`, and `softDeletesTz()` so the schema stays portable and timezone-aware; they map to `datetime` on SQLite.
- Keep `APP_TIMEZONE=UTC`; convert to the display timezone in the presentation layer only.
- Use `date` for calendar dates and the TZ-aware timestamp helpers for instants; do not store dates as strings.
- `useCurrent()` on a timestamp column is fine; SQLite resolves it to `CURRENT_TIMESTAMP`.

```php
Schema::create('pegawais', function (Blueprint $table) {
    $table->id();
    $table->string('nip')->unique();
    // ...
    $table->timestampsTz();
});
```

## Foreign keys

- SQLite only enforces foreign keys when `PRAGMA foreign_keys=ON`; Laravel enables it per connection through `foreign_key_constraints` (default `true`). Do not disable it.
- Every foreign key we define must restrict both update and delete:

```php
$table->foreignId('bidang_id')
    ->index()
    ->constrained('bidangs')
    ->restrictOnDelete()
    ->restrictOnUpdate();
```

- Call `->index()` on the column **before** `->constrained()`. After `constrained()` the fluent object is a `ForeignKeyDefinition`, so a later `->index()` is silently ignored.
- SQLite does not create an index for the referencing column. Add one unless an existing index already covers it (a unique constraint, or a composite index whose leading column is the FK column).
- The referenced side is already indexed by its primary or unique key; do not add a duplicate index.
- Leave foreign keys from framework/package migrations untouched; if a package FK lacks an index, fix it in a separate migration.

## Data types

- SQLite is dynamically typed, but Laravel's column types still drive casts and validation. Keep using `text` for free-form text and `string('column', n)` only when the length limit is a real business rule.
- `boolean` for flags, `numeric`/`decimal` for money, never `float`/`double` for exact values.
- `enum()` becomes `varchar` plus an inline `CHECK` constraint. Prefer a lookup table when the allowed values are likely to grow.
- `json` is stored as text; cast to `array`/`collection` in the model and query with SQLite's JSON functions only when necessary.
- `uuid`/`ulid` for public identifiers when needed; keep `id()` (bigint) for internal keys.

## Indexes

- Index columns used by real `WHERE`, `JOIN`, `ORDER BY`, and `GROUP BY` patterns; do not index every column.
- For composite indexes, put equality columns first and range/sort columns last.
- Partial and expression indexes are supported; use SQLite syntax when raw SQL is needed:

```php
DB::statement("create index pegawais_aktif_index on pegawais (id) where status = 'aktif'");
DB::statement('create unique index users_username_lower_unique on users (lower(username))');
```

- Lists ordered by time may need an index on `created_at`, or a composite such as `(status, created_at)`.
- SQLite has no `CREATE INDEX CONCURRENTLY`; create indexes inside the migration as usual.
- Confirm important indexes with `EXPLAIN QUERY PLAN` on realistic data before adding them.

## Migrations and constraints

- Wrap multi-statement data changes in `DB::transaction()`.
- SQLite cannot add or drop constraints after a table is created. Declare `CHECK` constraints inline with `Schema::create`; do not use `DB::statement('alter table ... add constraint ...')`.
- Enforce domain rules that SQLite cannot express as a constraint in Filament/FormRequest validation instead.
- SQLite has a limited `ALTER TABLE`; `->change()` rebuilds the table under the hood, so test it on realistic data.
- Implement `down()` so the migration can actually be reversed.
- Treat deployed migrations as immutable; add a new migration for later changes.

## Queries

- Use `cursorPaginate()` for large ordered result sets instead of deep `offset` pagination.
- Select only the columns you need, especially when the table has large `text` columns.
- SQLite allows a single writer at a time and `lockForUpdate()` is a no-op. Keep write transactions short and rely on the transaction boundary instead of row locks.
- Set a `busy_timeout` when concurrent writers are expected instead of retrying manually.
- Prefer `exists()` and `withCount()` over loading rows only to count or check them.
