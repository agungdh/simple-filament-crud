# PostgreSQL

This project runs on PostgreSQL only (Postgres 18 locally, and the same service in CI). Do not write SQLite/MySQL-only SQL or rely on their behaviour.

## Connection and raw SQL

- The app connects through `pgsql`; keep `DB_CONNECTION=pgsql` in `.env` and CI.
- Prefer the schema builder and Eloquent. When raw SQL is unavoidable, keep it PostgreSQL-valid, parameterised, and pass bindings to `DB::select()`/`DB::statement()` instead of interpolating values.
- Do not add SQLite/MySQL fallbacks to make a query pass.

## Timestamps

- Store instants as `timestamptz`, never `timestamp without time zone`.
- Use `timestampsTz()`, `timestampTz()`, `dateTimeTz()`, `timeTz()`, and `softDeletesTz()` instead of their non-TZ counterparts.

```php
Schema::create('pegawais', function (Blueprint $table) {
    $table->id();
    $table->string('nip')->unique();
    // ...
    $table->timestampsTz();
});
```

- Keep `APP_TIMEZONE=UTC`; convert to the display timezone in the presentation layer only.
- Use `date` for calendar dates and `timestamptz` for instants.
- `useCurrent()` on a `timestamptz` column is fine; PostgreSQL resolves it to `CURRENT_TIMESTAMP`.

## Foreign keys

- Every foreign key we define must restrict both update and delete:

```php
$table->foreignId('bidang_id')
    ->index()
    ->constrained('bidangs')
    ->restrictOnDelete()
    ->restrictOnUpdate();
```

- Call `->index()` on the column **before** `->constrained()`. After `constrained()` the fluent object is a `ForeignKeyDefinition`, so a later `->index()` is silently ignored.
- PostgreSQL does not create an index for the referencing column. Add one unless an existing index already covers it (a unique constraint, or a composite index whose leading column is the FK column).
- The referenced side is already indexed by its primary or unique key; do not add a duplicate index.
- Name indexes explicitly when Laravel's generated name would exceed PostgreSQL's 63-byte identifier limit.
- Leave foreign keys from framework/package migrations untouched; if a package FK lacks an index, fix it in a separate migration.

## Data types

- `jsonb` for JSON that is queried or indexed; `json` only for payloads that are never queried. Use a GIN index for containment queries (`DB::statement('create index ... using gin (meta)')`).
- `text` for free-form text. Use `string('column', n)` only when the length limit is a real business rule; `varchar(n)` has no performance advantage over `text` in PostgreSQL.
- `boolean` for flags, `numeric`/`decimal` for money, never `float`/`double` for exact values.
- `date` and `timestamptz` for temporal values; do not store dates as strings.
- `uuid`/`ulid` for public identifiers when needed; keep `id()` (bigint) for internal keys.
- `enum()` becomes `varchar + check` on PostgreSQL. Prefer a lookup table when the allowed values are likely to grow.
- PostgreSQL ignores `unsigned`; enforce non-negative values with a `check` constraint when the domain requires it.

## Indexes

- Index columns used by real `WHERE`, `JOIN`, `ORDER BY`, and `GROUP BY` patterns; do not index every column.
- For composite indexes, put equality columns first and range/sort columns last.
- Add partial indexes for hot subsets:

```php
DB::statement("create index pegawais_aktif_index on pegawais (id) where status = 'aktif'");
```

- Add expression indexes for case-insensitive lookups:

```php
DB::statement('create unique index users_username_lower_unique on users (lower(username))');
```

- Lists ordered by time may need an index on `created_at`, or a composite such as `(status, created_at)`.
- On large production tables, create indexes `concurrently` in a migration with `public bool $withinTransaction = false;`; `CREATE INDEX CONCURRENTLY` cannot run inside a transaction.
- Confirm important indexes with `EXPLAIN (ANALYZE, BUFFERS)` on realistic data before adding them.

## Migrations and constraints

- Wrap multi-statement data changes in `DB::transaction()`.
- Set `lock_timeout` before DDL on busy tables so a migration fails fast instead of blocking traffic.
- Add `NOT NULL` and `CHECK` constraints explicitly; PostgreSQL does not infer them.
- Be careful with `->change()`: some type changes rewrite the table. Test on realistic data and use `->using()` when the conversion needs an expression.
- Implement `down()` so the migration can actually be reversed.
- Treat deployed migrations as immutable; add a new migration for later changes.

## Queries

- Use `cursorPaginate()` for large ordered result sets instead of deep `offset` pagination.
- Select only the columns you need, especially when the table has large `text` or `jsonb` columns.
- Wrap read-modify-write sequences in a transaction and use `lockForUpdate()` when concurrent updates matter.
- Prefer `exists()` and `withCount()` over loading rows only to count or check them.
