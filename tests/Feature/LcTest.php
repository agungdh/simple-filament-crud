<?php

use App\Filament\Resources\Lcs\LcResource;
use App\Filament\Resources\Lcs\Pages\CreateLc;
use App\Filament\Resources\Lcs\Pages\EditLc;
use App\Filament\Resources\Lcs\Pages\ListLcs;
use App\Models\Lc;
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

it('allows an admin to list all lcs', function () {
    actingAs(User::factory()->admin()->create());

    $lcs = Lc::factory()->count(3)->create();

    Livewire::test(ListLcs::class)
        ->assertOk()
        ->assertCanSeeTableRecords($lcs);
});

it('only lists the lcs owned by the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();

    $own = Lc::factory()->create(['pegawai_id' => $pegawai->id]);
    $others = Lc::factory()->create(['pegawai_id' => $otherPegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListLcs::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$others]);
});

it('only shows the pegawai column to admins', function () {
    $pegawai = Pegawai::factory()->create();
    Lc::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListLcs::class)
        ->assertTableColumnHidden('pegawai.nama');

    actingAs(User::factory()->admin()->create());

    Livewire::test(ListLcs::class)
        ->assertTableColumnVisible('pegawai.nama');
});

it('does not link table rows to the edit page', function () {
    actingAs(User::factory()->admin()->create());

    $lc = Lc::factory()->create();

    $table = Livewire::test(ListLcs::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($lc))->toBeNull()
        ->and($table->getRecordAction($lc))->toBeNull();
});

it('creates an lc for the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();

    actingAs($pegawai->user);

    Livewire::test(CreateLc::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'materi_pengembangan' => 'LC Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(LcResource::getUrl('index'));

    assertDatabaseHas(Lc::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'LC Laravel',
        'jumlah_jam' => 4,
    ]);
});

it('allows an admin to create an lc for another pegawai', function () {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateLc::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'LC Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Lc::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'LC Laravel',
    ]);
});

it('requires the lc fields', function (string $field) {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateLc::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'LC Laravel',
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

it('can edit an owned lc', function () {
    $pegawai = Pegawai::factory()->create();
    $lc = Lc::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'LC Lama',
    ]);

    actingAs($pegawai->user);

    Livewire::test(EditLc::class, [
        'record' => $lc->getRouteKey(),
    ])
        ->fillForm([
            'materi_pengembangan' => 'LC Baru',
            'jumlah_jam' => 8,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(LcResource::getUrl('index'));

    assertDatabaseHas(Lc::class, [
        'id' => $lc->id,
        'materi_pengembangan' => 'LC Baru',
        'jumlah_jam' => 8,
    ]);
});

it('forbids a pegawai from editing another pegawai lc', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $lc = Lc::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('update', $lc))->toBeFalse();
});

it('can delete an owned lc from the table', function () {
    $pegawai = Pegawai::factory()->create();
    $lc = Lc::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListLcs::class)
        ->callAction(TestAction::make('delete')->table($lc))
        ->assertNotified();

    assertDatabaseMissing($lc);
});

it('forbids a pegawai from deleting another pegawai lc', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $lc = Lc::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('delete', $lc))->toBeFalse();
});
