# Testing

## Filament resource tests

- Every resource has a Pest feature test in `tests/Feature/{Model}Test.php` using `RefreshDatabase`.
- Cover listing, creating, `required` and `unique` validation, editing, and deleting.
- Test delete from the list page's table action:

```php
Livewire::test(ListBidangs::class)
    ->callAction(TestAction::make('delete')->table($bidang))
    ->assertNotified();
```

- Assert that create and edit redirect to the resource index with `assertRedirect(BidangResource::getUrl('index'))`.
- In `beforeEach`, authenticate with `actingAs(User::factory()->create())` and call `Filament::setCurrentPanel('admin')`.
- Use `Livewire::test()` because `pest-plugin-livewire` is not installed.
- When testing as a regular pegawai, assert only that their own records are visible. Do not assert the pegawai name/NIP column; a regular pegawai only sees their own records, so that column is admin-only. Cover its visibility in an admin test with `assertTableColumnVisible()`/`assertTableColumnHidden()`.
