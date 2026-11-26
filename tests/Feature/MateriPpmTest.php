<?php

use App\Filament\Resources\MateriPpms\MateriPpmResource;
use App\Filament\Resources\MateriPpms\Pages\CreateMateriPpm;
use App\Filament\Resources\MateriPpms\Pages\EditMateriPpm;
use App\Filament\Resources\MateriPpms\Pages\ListMateriPpms;
use App\Models\MateriPpm;
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

it('can list materi ppms', function () {
    $materiPpms = MateriPpm::factory()->count(3)->create();

    Livewire::test(ListMateriPpms::class)
        ->assertOk()
        ->assertCanSeeTableRecords($materiPpms);
});

it('does not link table rows to the edit page', function () {
    $materiPpm = MateriPpm::factory()->create();

    $table = Livewire::test(ListMateriPpms::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($materiPpm))->toBeNull()
        ->and($table->getRecordAction($materiPpm))->toBeNull();
});

it('can create a materi ppm', function () {
    Livewire::test(CreateMateriPpm::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'nomor_surat' => 'PPM-001',
            'nama_pemateri' => 'Budi Santoso',
            'materi_pengembangan' => 'Pengembangan Kompetensi',
            'tanggal_pelaksanaan' => '2026-03-01',
            'link_materi' => 'https://example.com/materi',
            'link_dokumentasi' => 'https://example.com/dokumentasi',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(MateriPpmResource::getUrl('index'));

    assertDatabaseHas(MateriPpm::class, [
        'nomor_surat' => 'PPM-001',
        'nama_pemateri' => 'Budi Santoso',
        'materi_pengembangan' => 'Pengembangan Kompetensi',
    ]);
});

it('can create a materi ppm without optional fields', function () {
    Livewire::test(CreateMateriPpm::class)
        ->fillForm([
            'nomor_surat' => null,
            'nama_pemateri' => null,
            'materi_pengembangan' => 'Pengembangan Kompetensi',
            'tanggal_pelaksanaan' => '2026-03-01',
            'link_materi' => null,
            'link_dokumentasi' => null,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(MateriPpm::class, [
        'nomor_surat' => null,
        'nama_pemateri' => null,
        'materi_pengembangan' => 'Pengembangan Kompetensi',
    ]);
});

it('requires the materi ppm fields', function (string $field) {
    Livewire::test(CreateMateriPpm::class)
        ->fillForm([
            'materi_pengembangan' => 'Pengembangan Kompetensi',
            'tanggal_pelaksanaan' => '2026-03-01',
            $field => null,
        ])
        ->call('create')
        ->assertHasFormErrors([$field => 'required'])
        ->assertNotNotified();
})->with([
    'materi_pengembangan',
    'tanggal_pelaksanaan',
]);

it('can edit a materi ppm', function () {
    $materiPpm = MateriPpm::factory()->create([
        'nomor_surat' => 'PPM-001',
    ]);

    Livewire::test(EditMateriPpm::class, [
        'record' => $materiPpm->getRouteKey(),
    ])
        ->fillForm([
            'nomor_surat' => 'PPM-002',
            'nama_pemateri' => 'Siti Aminah',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(MateriPpmResource::getUrl('index'));

    assertDatabaseHas(MateriPpm::class, [
        'id' => $materiPpm->id,
        'nomor_surat' => 'PPM-002',
        'nama_pemateri' => 'Siti Aminah',
    ]);
});

it('can delete a materi ppm from the table', function () {
    $materiPpm = MateriPpm::factory()->create();

    Livewire::test(ListMateriPpms::class)
        ->callAction(TestAction::make('delete')->table($materiPpm))
        ->assertNotified();

    assertDatabaseMissing($materiPpm);
});

it('forbids the pegawai role from accessing the materi ppm resource', function () {
    actingAs(User::factory()->pegawai()->create());

    $this->get(MateriPpmResource::getUrl('index'))->assertForbidden();
});
