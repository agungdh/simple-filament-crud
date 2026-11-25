# Filament Resources

This project uses Filament v5 with a single `admin` panel.

## CRUD conventions

- The `/` route redirects to the admin panel (`/admin`).
- Generate resources with `php artisan make:filament-resource {Model} --generate --no-interaction` after the migration exists.
- Keep the form and table definitions in `Schemas/{Model}Form.php` and `Tables/{Models}Table.php`; do not embed them in the resource class.
- Every resource table must expose row-level `EditAction` and `DeleteAction` in `recordActions()`.
- Tables must disable row-click navigation with `->recordUrl(null)` and `->recordAction(null)`; editing is only available through the row's `EditAction`.
- Delete is available only from the table. Do not add `DeleteAction` to the header actions of the edit page.
- When a record is referenced by another resource, disable its `DeleteAction` with a tooltip and exclude it from bulk selection with `checkIfRecordIsSelectableUsing()` instead of letting the foreign key throw an error.
- `Create` and `Edit` pages must override `getRedirectUrl()` so saving returns to the index:

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
```

- `Create` pages must disable the "create another" button with `protected static bool $canCreateAnother = false;`.

- Use `$recordTitleAttribute` so records are labelled by their main column, and set `$modelLabel`/`$pluralModelLabel` explicitly for Indonesian nouns that do not pluralize (e.g. `Bidang`).
- Put master data resources under the `Data Master` navigation group.

## Table density

- Tables use the dense row styling defined in `resources/css/filament/admin/theme.css`, since the UI is data-focused.
- New tables inherit this automatically through the panel theme; do not add per-table density code.
- Run `npm run build` after changing the theme CSS so the compiled panel assets are updated.

## Models

- Use the `#[Fillable([...])]` and `#[Hidden([...])]` attributes instead of the `$fillable`/`$hidden` properties.
- Add a factory and a seeder for every new model.
