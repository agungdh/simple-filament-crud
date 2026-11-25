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
