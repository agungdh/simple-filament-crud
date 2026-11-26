<?php

use App\Filament\Resources\Bidangs\BidangResource;
use App\Filament\Resources\Bidangs\Pages\CreateBidang;
use App\Filament\Resources\Bidangs\Pages\EditBidang;
use App\Filament\Resources\Bidangs\Pages\ListBidangs;
use App\Models\Bidang;
use App\Models\Pegawai;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    Filament::setCurrentPanel('admin');

    actingAs(User::factory()->create());
});

it('can list bidangs', function () {
    $bidangs = Bidang::factory()->count(3)->create();

    Livewire::test(ListBidangs::class)
        ->assertOk()
        ->assertCanSeeTableRecords($bidangs);
});

it('does not link table rows to the edit page', function () {
    $bidang = Bidang::factory()->create();

    $table = Livewire::test(ListBidangs::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($bidang))->toBeNull()
        ->and($table->getRecordAction($bidang))->toBeNull();
});

it('can create a bidang', function () {
    Livewire::test(CreateBidang::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'bidang' => 'Keuangan',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(BidangResource::getUrl('index'));

    assertDatabaseHas(Bidang::class, [
        'bidang' => 'Keuangan',
    ]);
});

it('requires a bidang', function () {
    Livewire::test(CreateBidang::class)
        ->fillForm([
            'bidang' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['bidang' => 'required'])
        ->assertNotNotified();
});

it('requires a unique bidang', function () {
    Bidang::factory()->create([
        'bidang' => 'Keuangan',
    ]);

    Livewire::test(CreateBidang::class)
        ->fillForm([
            'bidang' => 'Keuangan',
        ])
        ->call('create')
        ->assertHasFormErrors(['bidang' => 'unique'])
        ->assertNotNotified();
});

it('can edit a bidang', function () {
    $bidang = Bidang::factory()->create([
        'bidang' => 'Keuangan',
    ]);

    Livewire::test(EditBidang::class, [
        'record' => $bidang->getRouteKey(),
    ])
        ->fillForm([
            'bidang' => 'Keuangan dan Aset',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(BidangResource::getUrl('index'));

    assertDatabaseHas(Bidang::class, [
        'id' => $bidang->id,
        'bidang' => 'Keuangan dan Aset',
    ]);
});

it('can delete a bidang from the table', function () {
    $bidang = Bidang::factory()->create();

    Livewire::test(ListBidangs::class)
        ->callAction(TestAction::make('delete')->table($bidang))
        ->assertNotified();

    assertDatabaseMissing($bidang);
});

it('cannot delete a bidang that is used by a pegawai', function () {
    $bidang = Bidang::factory()->create();
    Pegawai::factory()->create([
        'bidang_id' => $bidang->id,
    ]);

    Livewire::test(ListBidangs::class)
        ->assertActionDisabled(TestAction::make('delete')->table($bidang));

    assertDatabaseHas(Bidang::class, [
        'id' => $bidang->id,
    ]);
});
