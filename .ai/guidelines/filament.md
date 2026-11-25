# Filament Resources

This project uses Filament v5 with a single `admin` panel.

## CRUD conventions

- Generate resources with `php artisan make:filament-resource {Model} --generate --no-interaction` after the migration exists.
- Keep the form and table definitions in `Schemas/{Model}Form.php` and `Tables/{Models}Table.php`; do not embed them in the resource class.
- Every resource table must expose row-level `EditAction` and `DeleteAction` in `recordActions()`.
- Delete is available only from the table. Do not add `DeleteAction` to the header actions of the edit page.
- `Create` and `Edit` pages must override `getRedirectUrl()` so saving returns to the index:

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
```

- Use `$recordTitleAttribute` so records are labelled by their main column, and set `$modelLabel`/`$pluralModelLabel` explicitly for Indonesian nouns that do not pluralize (e.g. `Bidang`).
- Put master data resources under the `Data Master` navigation group.

## Models

- Use the `#[Fillable([...])]` and `#[Hidden([...])]` attributes instead of the `$fillable`/`$hidden` properties.
- Add a factory and a seeder for every new model.
