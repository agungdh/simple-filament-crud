<?php

use App\Filament\Resources\Diklats\DiklatResource;
use App\Filament\Resources\Diklats\Pages\CreateDiklat;
use App\Filament\Resources\Diklats\Pages\EditDiklat;
use App\Filament\Resources\Diklats\Pages\ListDiklats;
use App\Models\Diklat;
use App\Models\JenisPelatihan;
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

it('can list diklats', function () {
    $diklats = Diklat::factory()->count(3)->create();

    Livewire::test(ListDiklats::class)
        ->assertOk()
        ->assertCanSeeTableRecords($diklats);
});

it('does not link table rows to the edit page', function () {
    $diklat = Diklat::factory()->create();

    $table = Livewire::test(ListDiklats::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($diklat))->toBeNull()
        ->and($table->getRecordAction($diklat))->toBeNull();
});

it('can create a diklat', function () {
    $pegawai = Pegawai::factory()->create();
    $jenisPelatihan = JenisPelatihan::factory()->create();

    Livewire::test(CreateDiklat::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'jenis_pelatihan_id' => $jenisPelatihan->id,
            'nomor_surat' => 'Diklat-001',
            'materi_pengembangan' => 'Pengembangan Kompetensi',
            'dari_tanggal_pelaksanaan' => '2026-01-05',
            'sampai_tanggal_pelaksanaan' => '2026-01-07',
            'jumlah_jam_pelatihan' => 24,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(DiklatResource::getUrl('index'));

    assertDatabaseHas(Diklat::class, [
        'pegawai_id' => $pegawai->id,
        'jenis_pelatihan_id' => $jenisPelatihan->id,
        'nomor_surat' => 'Diklat-001',
        'jumlah_jam_pelatihan' => 24,
    ]);
});

it('requires the diklat fields', function (string $field) {
    $pegawai = Pegawai::factory()->create();
    $jenisPelatihan = JenisPelatihan::factory()->create();

    Livewire::test(CreateDiklat::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'jenis_pelatihan_id' => $jenisPelatihan->id,
            'nomor_surat' => 'Diklat-001',
            'materi_pengembangan' => 'Pengembangan Kompetensi',
            'dari_tanggal_pelaksanaan' => '2026-01-05',
            'sampai_tanggal_pelaksanaan' => '2026-01-07',
            'jumlah_jam_pelatihan' => 24,
            $field => null,
        ])
        ->call('create')
        ->assertHasFormErrors([$field => 'required'])
        ->assertNotNotified();
})->with([
    'pegawai_id',
    'jenis_pelatihan_id',
    'nomor_surat',
    'materi_pengembangan',
    'dari_tanggal_pelaksanaan',
    'sampai_tanggal_pelaksanaan',
    'jumlah_jam_pelatihan',
]);

it('rejects a diklat whose start date is after its end date', function () {
    $pegawai = Pegawai::factory()->create();
    $jenisPelatihan = JenisPelatihan::factory()->create();

    Livewire::test(CreateDiklat::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'jenis_pelatihan_id' => $jenisPelatihan->id,
            'nomor_surat' => 'Diklat-001',
            'materi_pengembangan' => 'Pengembangan Kompetensi',
            'dari_tanggal_pelaksanaan' => '2026-01-10',
            'sampai_tanggal_pelaksanaan' => '2026-01-05',
            'jumlah_jam_pelatihan' => 24,
        ])
        ->call('create')
        ->assertHasFormErrors(['dari_tanggal_pelaksanaan'])
        ->assertNotNotified();
});

it('can edit a diklat', function () {
    $diklat = Diklat::factory()->create([
        'nomor_surat' => 'Diklat-001',
    ]);
    $pegawai = Pegawai::factory()->create();

    Livewire::test(EditDiklat::class, [
        'record' => $diklat->getRouteKey(),
    ])
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'Diklat-002',
            'jumlah_jam_pelatihan' => 32,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(DiklatResource::getUrl('index'));

    assertDatabaseHas(Diklat::class, [
        'id' => $diklat->id,
        'pegawai_id' => $pegawai->id,
        'nomor_surat' => 'Diklat-002',
        'jumlah_jam_pelatihan' => 32,
    ]);
});

it('can delete a diklat from the table', function () {
    $diklat = Diklat::factory()->create();

    Livewire::test(ListDiklats::class)
        ->callAction(TestAction::make('delete')->table($diklat))
        ->assertNotified();

    assertDatabaseMissing($diklat);
});

it('forbids the pegawai role from accessing the diklat resource', function () {
    actingAs(User::factory()->pegawai()->create());

    $this->get(DiklatResource::getUrl('index'))->assertForbidden();
});
