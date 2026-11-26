<?php

use App\Filament\Resources\PangkatGolongans\Pages\CreatePangkatGolongan;
use App\Filament\Resources\PangkatGolongans\Pages\EditPangkatGolongan;
use App\Filament\Resources\PangkatGolongans\Pages\ListPangkatGolongans;
use App\Filament\Resources\PangkatGolongans\PangkatGolonganResource;
use App\Models\PangkatGolongan;
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

    actingAs(User::factory()->admin()->create());
});

it('can list pangkat golongans', function () {
    $pangkats = PangkatGolongan::factory()->count(3)->create();

    Livewire::test(ListPangkatGolongans::class)
        ->assertOk()
        ->assertCanSeeTableRecords($pangkats);
});

it('does not link table rows to the edit page', function () {
    $pangkat = PangkatGolongan::factory()->create();

    $table = Livewire::test(ListPangkatGolongans::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($pangkat))->toBeNull()
        ->and($table->getRecordAction($pangkat))->toBeNull();
});

it('can create a pangkat golongan', function () {
    Livewire::test(CreatePangkatGolongan::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'jenjang' => 'Penata',
            'pangkat' => 'Penata Muda',
            'golongan' => 'III',
            'ruang' => 'a',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(PangkatGolonganResource::getUrl('index'));

    assertDatabaseHas(PangkatGolongan::class, [
        'jenjang' => 'Penata',
        'pangkat' => 'Penata Muda',
        'golongan' => 'III',
        'ruang' => 'a',
    ]);
});

it('requires the pangkat golongan fields', function (string $field) {
    Livewire::test(CreatePangkatGolongan::class)
        ->fillForm([
            'jenjang' => 'Penata',
            'pangkat' => 'Penata Muda',
            'golongan' => 'III',
            'ruang' => 'a',
            $field => null,
        ])
        ->call('create')
        ->assertHasFormErrors([$field => 'required'])
        ->assertNotNotified();
})->with(['jenjang', 'pangkat', 'golongan', 'ruang']);

it('can edit a pangkat golongan', function () {
    $pangkat = PangkatGolongan::factory()->create([
        'pangkat' => 'Penata Muda',
    ]);

    Livewire::test(EditPangkatGolongan::class, [
        'record' => $pangkat->getRouteKey(),
    ])
        ->fillForm([
            'pangkat' => 'Penata Muda Tingkat I',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(PangkatGolonganResource::getUrl('index'));

    assertDatabaseHas(PangkatGolongan::class, [
        'id' => $pangkat->id,
        'pangkat' => 'Penata Muda Tingkat I',
    ]);
});

it('can delete a pangkat golongan from the table', function () {
    $pangkat = PangkatGolongan::factory()->create();

    Livewire::test(ListPangkatGolongans::class)
        ->callAction(TestAction::make('delete')->table($pangkat))
        ->assertNotified();

    assertDatabaseMissing($pangkat);
});

it('cannot delete a pangkat golongan that is used by a pegawai', function () {
    $pangkat = PangkatGolongan::factory()->create();
    Pegawai::factory()->create([
        'pangkat_golongan_id' => $pangkat->id,
    ]);

    Livewire::test(ListPangkatGolongans::class)
        ->assertActionDisabled(TestAction::make('delete')->table($pangkat));

    assertDatabaseHas(PangkatGolongan::class, [
        'id' => $pangkat->id,
    ]);
});

it('forbids the pegawai role from accessing the pangkat golongan resource', function () {
    actingAs(User::factory()->pegawai()->create());

    $this->get(PangkatGolonganResource::getUrl('index'))->assertForbidden();
});
