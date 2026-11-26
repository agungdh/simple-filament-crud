<?php

use App\Filament\Resources\BelajarMandiris\BelajarMandiriResource;
use App\Filament\Resources\BelajarMandiris\Pages\CreateBelajarMandiri;
use App\Filament\Resources\BelajarMandiris\Pages\EditBelajarMandiri;
use App\Filament\Resources\BelajarMandiris\Pages\ListBelajarMandiris;
use App\Models\BelajarMandiri;
use App\Models\Pegawai;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
});

it('allows an admin to list all belajar mandiris', function () {
    actingAs(User::factory()->admin()->create());

    $belajarMandiris = BelajarMandiri::factory()->count(3)->create();

    Livewire::test(ListBelajarMandiris::class)
        ->assertOk()
        ->assertCanSeeTableRecords($belajarMandiris);
});

it('only lists the belajar mandiris owned by the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();

    $own = BelajarMandiri::factory()->create(['pegawai_id' => $pegawai->id]);
    $others = BelajarMandiri::factory()->create(['pegawai_id' => $otherPegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListBelajarMandiris::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$others]);
});

it('does not link table rows to the edit page', function () {
    actingAs(User::factory()->admin()->create());

    $belajarMandiri = BelajarMandiri::factory()->create();

    $table = Livewire::test(ListBelajarMandiris::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($belajarMandiri))->toBeNull()
        ->and($table->getRecordAction($belajarMandiri))->toBeNull();
});

it('creates a belajar mandiri for the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();

    actingAs($pegawai->user);

    Livewire::test(CreateBelajarMandiri::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'materi_pengembangan' => 'Belajar Mandiri Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(BelajarMandiriResource::getUrl('index'));

    assertDatabaseHas(BelajarMandiri::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Belajar Mandiri Laravel',
        'jumlah_jam' => 4,
    ]);
});

it('allows an admin to create a belajar mandiri for another pegawai', function () {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateBelajarMandiri::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Belajar Mandiri Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(BelajarMandiri::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Belajar Mandiri Laravel',
    ]);
});

it('requires the belajar mandiri fields', function (string $field) {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateBelajarMandiri::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Belajar Mandiri Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
            $field => null,
        ])
        ->call('create')
        ->assertHasFormErrors([$field => 'required'])
        ->assertNotNotified();
})->with([
    'pegawai_id',
    'materi_pengembangan',
    'tanggal_pelaksanaan',
    'jumlah_jam',
]);

it('can edit an owned belajar mandiri', function () {
    $pegawai = Pegawai::factory()->create();
    $belajarMandiri = BelajarMandiri::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Belajar Mandiri Lama',
    ]);

    actingAs($pegawai->user);

    Livewire::test(EditBelajarMandiri::class, [
        'record' => $belajarMandiri->getRouteKey(),
    ])
        ->fillForm([
            'materi_pengembangan' => 'Belajar Mandiri Baru',
            'jumlah_jam' => 8,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(BelajarMandiriResource::getUrl('index'));

    assertDatabaseHas(BelajarMandiri::class, [
        'id' => $belajarMandiri->id,
        'materi_pengembangan' => 'Belajar Mandiri Baru',
        'jumlah_jam' => 8,
    ]);
});

it('forbids a pegawai from editing another pegawai belajar mandiri', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $belajarMandiri = BelajarMandiri::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('update', $belajarMandiri))->toBeFalse();
});

it('can delete an owned belajar mandiri from the table', function () {
    $pegawai = Pegawai::factory()->create();
    $belajarMandiri = BelajarMandiri::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListBelajarMandiris::class)
        ->callAction(TestAction::make('delete')->table($belajarMandiri))
        ->assertNotified();

    assertDatabaseMissing($belajarMandiri);
});

it('forbids a pegawai from deleting another pegawai belajar mandiri', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $belajarMandiri = BelajarMandiri::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('delete', $belajarMandiri))->toBeFalse();
});
