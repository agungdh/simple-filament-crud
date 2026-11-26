<?php

use App\Filament\Resources\Workshops\Pages\CreateWorkshop;
use App\Filament\Resources\Workshops\Pages\EditWorkshop;
use App\Filament\Resources\Workshops\Pages\ListWorkshops;
use App\Filament\Resources\Workshops\WorkshopResource;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Workshop;
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

it('allows an admin to list all workshops', function () {
    actingAs(User::factory()->admin()->create());

    $workshops = Workshop::factory()->count(3)->create();

    Livewire::test(ListWorkshops::class)
        ->assertOk()
        ->assertCanSeeTableRecords($workshops);
});

it('only lists the workshops owned by the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();

    $own = Workshop::factory()->create(['pegawai_id' => $pegawai->id]);
    $others = Workshop::factory()->create(['pegawai_id' => $otherPegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListWorkshops::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$others]);
});

it('does not link table rows to the edit page', function () {
    actingAs(User::factory()->admin()->create());

    $workshop = Workshop::factory()->create();

    $table = Livewire::test(ListWorkshops::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($workshop))->toBeNull()
        ->and($table->getRecordAction($workshop))->toBeNull();
});

it('creates a workshop for the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();

    actingAs($pegawai->user);

    Livewire::test(CreateWorkshop::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'materi_pengembangan' => 'Workshop Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(WorkshopResource::getUrl('index'));

    assertDatabaseHas(Workshop::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Workshop Laravel',
        'jumlah_jam' => 4,
    ]);
});

it('allows an admin to create a workshop for another pegawai', function () {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateWorkshop::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Workshop Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Workshop::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Workshop Laravel',
    ]);
});

it('requires the workshop fields', function (string $field) {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateWorkshop::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Workshop Laravel',
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

it('can edit an owned workshop', function () {
    $pegawai = Pegawai::factory()->create();
    $workshop = Workshop::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Workshop Lama',
    ]);

    actingAs($pegawai->user);

    Livewire::test(EditWorkshop::class, [
        'record' => $workshop->getRouteKey(),
    ])
        ->fillForm([
            'materi_pengembangan' => 'Workshop Baru',
            'jumlah_jam' => 8,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(WorkshopResource::getUrl('index'));

    assertDatabaseHas(Workshop::class, [
        'id' => $workshop->id,
        'materi_pengembangan' => 'Workshop Baru',
        'jumlah_jam' => 8,
    ]);
});

it('forbids a pegawai from editing another pegawai workshop', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $workshop = Workshop::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('update', $workshop))->toBeFalse();
});

it('can delete an owned workshop from the table', function () {
    $pegawai = Pegawai::factory()->create();
    $workshop = Workshop::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListWorkshops::class)
        ->callAction(TestAction::make('delete')->table($workshop))
        ->assertNotified();

    assertDatabaseMissing($workshop);
});

it('forbids a pegawai from deleting another pegawai workshop', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $workshop = Workshop::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('delete', $workshop))->toBeFalse();
});
