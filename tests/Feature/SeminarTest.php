<?php

use App\Filament\Resources\Seminars\Pages\CreateSeminar;
use App\Filament\Resources\Seminars\Pages\EditSeminar;
use App\Filament\Resources\Seminars\Pages\ListSeminars;
use App\Filament\Resources\Seminars\SeminarResource;
use App\Models\Pegawai;
use App\Models\Seminar;
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

it('allows an admin to list all seminars', function () {
    actingAs(User::factory()->admin()->create());

    $seminars = Seminar::factory()->count(3)->create();

    Livewire::test(ListSeminars::class)
        ->assertOk()
        ->assertCanSeeTableRecords($seminars);
});

it('only lists the seminars owned by the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();

    $own = Seminar::factory()->create(['pegawai_id' => $pegawai->id]);
    $others = Seminar::factory()->create(['pegawai_id' => $otherPegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListSeminars::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$others]);
});

it('only shows the pegawai column to admins', function () {
    $pegawai = Pegawai::factory()->create();
    Seminar::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListSeminars::class)
        ->assertTableColumnHidden('pegawai.nama');

    actingAs(User::factory()->admin()->create());

    Livewire::test(ListSeminars::class)
        ->assertTableColumnVisible('pegawai.nama');
});

it('does not link table rows to the edit page', function () {
    actingAs(User::factory()->admin()->create());

    $seminar = Seminar::factory()->create();

    $table = Livewire::test(ListSeminars::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($seminar))->toBeNull()
        ->and($table->getRecordAction($seminar))->toBeNull();
});

it('creates a seminar for the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();

    actingAs($pegawai->user);

    Livewire::test(CreateSeminar::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'materi_pengembangan' => 'Seminar Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(SeminarResource::getUrl('index'));

    assertDatabaseHas(Seminar::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Seminar Laravel',
        'jumlah_jam' => 4,
    ]);
});

it('allows an admin to create a seminar for another pegawai', function () {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateSeminar::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Seminar Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Seminar::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Seminar Laravel',
    ]);
});

it('requires the seminar fields', function (string $field) {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateSeminar::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Seminar Laravel',
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

it('can edit an owned seminar', function () {
    $pegawai = Pegawai::factory()->create();
    $seminar = Seminar::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Seminar Lama',
    ]);

    actingAs($pegawai->user);

    Livewire::test(EditSeminar::class, [
        'record' => $seminar->getRouteKey(),
    ])
        ->fillForm([
            'materi_pengembangan' => 'Seminar Baru',
            'jumlah_jam' => 8,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(SeminarResource::getUrl('index'));

    assertDatabaseHas(Seminar::class, [
        'id' => $seminar->id,
        'materi_pengembangan' => 'Seminar Baru',
        'jumlah_jam' => 8,
    ]);
});

it('forbids a pegawai from editing another pegawai seminar', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $seminar = Seminar::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('update', $seminar))->toBeFalse();
});

it('can delete an owned seminar from the table', function () {
    $pegawai = Pegawai::factory()->create();
    $seminar = Seminar::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListSeminars::class)
        ->callAction(TestAction::make('delete')->table($seminar))
        ->assertNotified();

    assertDatabaseMissing($seminar);
});

it('forbids a pegawai from deleting another pegawai seminar', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $seminar = Seminar::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('delete', $seminar))->toBeFalse();
});
