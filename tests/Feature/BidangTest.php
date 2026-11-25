<?php

use App\Filament\Resources\Bidangs\Pages\CreateBidang;
use App\Filament\Resources\Bidangs\Pages\EditBidang;
use App\Filament\Resources\Bidangs\Pages\ListBidangs;
use App\Models\Bidang;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

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

it('can create a bidang', function () {
    Livewire::test(CreateBidang::class)
        ->fillForm([
            'bidang' => 'Keuangan',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

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
        ->assertNotified();

    assertDatabaseHas(Bidang::class, [
        'id' => $bidang->id,
        'bidang' => 'Keuangan dan Aset',
    ]);
});

it('can delete a bidang', function () {
    $bidang = Bidang::factory()->create();

    Livewire::test(EditBidang::class, [
        'record' => $bidang->getRouteKey(),
    ])
        ->callAction(DeleteAction::class)
        ->assertNotified();

    assertDatabaseMissing($bidang);
});
