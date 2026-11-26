<?php

use App\Filament\Resources\Ppms\Pages\CreatePpm;
use App\Filament\Resources\Ppms\Pages\EditPpm;
use App\Filament\Resources\Ppms\Pages\ListPpms;
use App\Filament\Resources\Ppms\PpmResource;
use App\Models\Pegawai;
use App\Models\Ppm;
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

it('can list ppms', function () {
    $ppms = Ppm::factory()->count(3)->create();

    Livewire::test(ListPpms::class)
        ->assertOk()
        ->assertCanSeeTableRecords($ppms);
});

it('does not link table rows to the edit page', function () {
    $ppm = Ppm::factory()->create();

    $table = Livewire::test(ListPpms::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($ppm))->toBeNull()
        ->and($table->getRecordAction($ppm))->toBeNull();
});

it('can create a ppm', function () {
    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreatePpm::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'PPM-001',
            'materi_pengembangan' => 'Pengembangan Kompetensi',
            'tanggal_pelaksanaan' => '2026-02-10',
            'jumlah_jam_pelatihan' => 16,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(PpmResource::getUrl('index'));

    assertDatabaseHas(Ppm::class, [
        'pegawai_id' => $pegawai->id,
        'nomor_surat' => 'PPM-001',
        'jumlah_jam_pelatihan' => 16,
    ]);
});

it('requires the ppm fields', function (string $field) {
    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreatePpm::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'PPM-001',
            'materi_pengembangan' => 'Pengembangan Kompetensi',
            'tanggal_pelaksanaan' => '2026-02-10',
            'jumlah_jam_pelatihan' => 16,
            $field => null,
        ])
        ->call('create')
        ->assertHasFormErrors([$field => 'required'])
        ->assertNotNotified();
})->with([
    'pegawai_id',
    'nomor_surat',
    'materi_pengembangan',
    'tanggal_pelaksanaan',
    'jumlah_jam_pelatihan',
]);

it('can edit a ppm', function () {
    $ppm = Ppm::factory()->create([
        'nomor_surat' => 'PPM-001',
    ]);
    $pegawai = Pegawai::factory()->create();

    Livewire::test(EditPpm::class, [
        'record' => $ppm->getRouteKey(),
    ])
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'PPM-002',
            'jumlah_jam_pelatihan' => 20,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(PpmResource::getUrl('index'));

    assertDatabaseHas(Ppm::class, [
        'id' => $ppm->id,
        'pegawai_id' => $pegawai->id,
        'nomor_surat' => 'PPM-002',
        'jumlah_jam_pelatihan' => 20,
    ]);
});

it('can delete a ppm from the table', function () {
    $ppm = Ppm::factory()->create();

    Livewire::test(ListPpms::class)
        ->callAction(TestAction::make('delete')->table($ppm))
        ->assertNotified();

    assertDatabaseMissing($ppm);
});

it('forbids the pegawai role from accessing the ppm resource', function () {
    actingAs(User::factory()->pegawai()->create());

    $this->get(PpmResource::getUrl('index'))->assertForbidden();
});
