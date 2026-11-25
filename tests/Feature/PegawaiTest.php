<?php

use App\Filament\Resources\Pegawais\Pages\CreatePegawai;
use App\Filament\Resources\Pegawais\Pages\EditPegawai;
use App\Filament\Resources\Pegawais\Pages\ListPegawais;
use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Models\Bidang;
use App\Models\Pegawai;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
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

it('can list pegawais', function () {
    $pegawais = Pegawai::factory()->count(3)->create();

    Livewire::test(ListPegawais::class)
        ->assertOk()
        ->assertCanSeeTableRecords($pegawais);
});

it('does not link table rows to the edit page', function () {
    $pegawai = Pegawai::factory()->create();

    $table = Livewire::test(ListPegawais::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($pegawai))->toBeNull()
        ->and($table->getRecordAction($pegawai))->toBeNull();
});

it('can filter pegawais by bidang', function () {
    $bidang = Bidang::factory()->create();
    $matchingPegawai = Pegawai::factory()->create([
        'bidang_id' => $bidang->id,
    ]);
    $otherPegawai = Pegawai::factory()->create();

    Livewire::test(ListPegawais::class)
        ->filterTable('bidang_id', $bidang->id)
        ->assertCanSeeTableRecords([$matchingPegawai])
        ->assertCanNotSeeTableRecords([$otherPegawai]);
});

it('can create a pegawai', function () {
    $bidang = Bidang::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'nama' => 'Budi Santoso',
            'nip' => '198501012010011001',
            'bidang_id' => $bidang->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(PegawaiResource::getUrl('index'));

    assertDatabaseHas(Pegawai::class, [
        'nama' => 'Budi Santoso',
        'nip' => '198501012010011001',
        'bidang_id' => $bidang->id,
    ]);
});

it('requires the pegawai fields', function (string $field) {
    $bidang = Bidang::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->fillForm([
            'nama' => 'Budi Santoso',
            'nip' => '198501012010011001',
            'bidang_id' => $bidang->id,
            $field => null,
        ])
        ->call('create')
        ->assertHasFormErrors([$field => 'required'])
        ->assertNotNotified();
})->with(['nama', 'nip', 'bidang_id']);

it('requires a unique nip', function () {
    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->fillForm([
            'nama' => 'Budi Santoso',
            'nip' => $pegawai->nip,
            'bidang_id' => $pegawai->bidang_id,
        ])
        ->call('create')
        ->assertHasFormErrors(['nip' => 'unique'])
        ->assertNotNotified();
});

it('can edit a pegawai', function () {
    $pegawai = Pegawai::factory()->create([
        'nama' => 'Budi',
    ]);
    $bidang = Bidang::factory()->create();

    Livewire::test(EditPegawai::class, [
        'record' => $pegawai->getRouteKey(),
    ])
        ->fillForm([
            'nama' => 'Budi Santoso',
            'bidang_id' => $bidang->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(PegawaiResource::getUrl('index'));

    assertDatabaseHas(Pegawai::class, [
        'id' => $pegawai->id,
        'nama' => 'Budi Santoso',
        'bidang_id' => $bidang->id,
    ]);
});

it('can delete a pegawai from the table', function () {
    $pegawai = Pegawai::factory()->create();

    Livewire::test(ListPegawais::class)
        ->callAction(TestAction::make('delete')->table($pegawai))
        ->assertNotified();

    assertDatabaseMissing($pegawai);
});
