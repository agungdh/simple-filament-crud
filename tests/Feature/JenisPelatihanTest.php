<?php

use App\Filament\Resources\JenisPelatihans\JenisPelatihanResource;
use App\Filament\Resources\JenisPelatihans\Pages\CreateJenisPelatihan;
use App\Filament\Resources\JenisPelatihans\Pages\EditJenisPelatihan;
use App\Filament\Resources\JenisPelatihans\Pages\ListJenisPelatihans;
use App\Models\Diklat;
use App\Models\JenisPelatihan;
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

it('can list jenis pelatihans', function () {
    $jenisPelatihans = JenisPelatihan::factory()->count(3)->create();

    Livewire::test(ListJenisPelatihans::class)
        ->assertOk()
        ->assertCanSeeTableRecords($jenisPelatihans);
});

it('does not link table rows to the edit page', function () {
    $jenisPelatihan = JenisPelatihan::factory()->create();

    $table = Livewire::test(ListJenisPelatihans::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($jenisPelatihan))->toBeNull()
        ->and($table->getRecordAction($jenisPelatihan))->toBeNull();
});

it('can create a jenis pelatihan', function () {
    Livewire::test(CreateJenisPelatihan::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'jenis_pelatihan' => 'Penjenjangan',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(JenisPelatihanResource::getUrl('index'));

    assertDatabaseHas(JenisPelatihan::class, [
        'jenis_pelatihan' => 'Penjenjangan',
    ]);
});

it('requires a jenis pelatihan', function () {
    Livewire::test(CreateJenisPelatihan::class)
        ->fillForm([
            'jenis_pelatihan' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['jenis_pelatihan' => 'required'])
        ->assertNotNotified();
});

it('requires a unique jenis pelatihan', function () {
    JenisPelatihan::factory()->create([
        'jenis_pelatihan' => 'MOOC',
    ]);

    Livewire::test(CreateJenisPelatihan::class)
        ->fillForm([
            'jenis_pelatihan' => 'MOOC',
        ])
        ->call('create')
        ->assertHasFormErrors(['jenis_pelatihan' => 'unique'])
        ->assertNotNotified();
});

it('can edit a jenis pelatihan', function () {
    $jenisPelatihan = JenisPelatihan::factory()->create([
        'jenis_pelatihan' => 'MOOC',
    ]);

    Livewire::test(EditJenisPelatihan::class, [
        'record' => $jenisPelatihan->getRouteKey(),
    ])
        ->fillForm([
            'jenis_pelatihan' => 'Sertifikasi',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(JenisPelatihanResource::getUrl('index'));

    assertDatabaseHas(JenisPelatihan::class, [
        'id' => $jenisPelatihan->id,
        'jenis_pelatihan' => 'Sertifikasi',
    ]);
});

it('can delete a jenis pelatihan from the table', function () {
    $jenisPelatihan = JenisPelatihan::factory()->create();

    Livewire::test(ListJenisPelatihans::class)
        ->callAction(TestAction::make('delete')->table($jenisPelatihan))
        ->assertNotified();

    assertDatabaseMissing($jenisPelatihan);
});

it('cannot delete a jenis pelatihan that is used by a diklat', function () {
    $jenisPelatihan = JenisPelatihan::factory()->create();
    Diklat::factory()->create([
        'jenis_pelatihan_id' => $jenisPelatihan->id,
    ]);

    Livewire::test(ListJenisPelatihans::class)
        ->assertActionDisabled(TestAction::make('delete')->table($jenisPelatihan));

    assertDatabaseHas(JenisPelatihan::class, [
        'id' => $jenisPelatihan->id,
    ]);
});

it('forbids the pegawai role from accessing the jenis pelatihan resource', function () {
    actingAs(User::factory()->pegawai()->create());

    $this->get(JenisPelatihanResource::getUrl('index'))->assertForbidden();
});
