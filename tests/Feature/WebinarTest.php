<?php

use App\Filament\Resources\Webinars\Pages\CreateWebinar;
use App\Filament\Resources\Webinars\Pages\EditWebinar;
use App\Filament\Resources\Webinars\Pages\ListWebinars;
use App\Filament\Resources\Webinars\WebinarResource;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Webinar;
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

it('allows an admin to list all webinars', function () {
    actingAs(User::factory()->admin()->create());

    $webinars = Webinar::factory()->count(3)->create();

    Livewire::test(ListWebinars::class)
        ->assertOk()
        ->assertCanSeeTableRecords($webinars);
});

it('only lists the webinars owned by the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();

    $own = Webinar::factory()->create(['pegawai_id' => $pegawai->id]);
    $others = Webinar::factory()->create(['pegawai_id' => $otherPegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListWebinars::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$others]);
});

it('does not link table rows to the edit page', function () {
    actingAs(User::factory()->admin()->create());

    $webinar = Webinar::factory()->create();

    $table = Livewire::test(ListWebinars::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($webinar))->toBeNull()
        ->and($table->getRecordAction($webinar))->toBeNull();
});

it('creates a webinar for the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();

    actingAs($pegawai->user);

    Livewire::test(CreateWebinar::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'materi_pengembangan' => 'Webinar Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(WebinarResource::getUrl('index'));

    assertDatabaseHas(Webinar::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Webinar Laravel',
        'jumlah_jam' => 4,
    ]);
});

it('allows an admin to create a webinar for another pegawai', function () {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateWebinar::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Webinar Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Webinar::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Webinar Laravel',
    ]);
});

it('requires the webinar fields', function (string $field) {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateWebinar::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Webinar Laravel',
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

it('can edit an owned webinar', function () {
    $pegawai = Pegawai::factory()->create();
    $webinar = Webinar::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Webinar Lama',
    ]);

    actingAs($pegawai->user);

    Livewire::test(EditWebinar::class, [
        'record' => $webinar->getRouteKey(),
    ])
        ->fillForm([
            'materi_pengembangan' => 'Webinar Baru',
            'jumlah_jam' => 8,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(WebinarResource::getUrl('index'));

    assertDatabaseHas(Webinar::class, [
        'id' => $webinar->id,
        'materi_pengembangan' => 'Webinar Baru',
        'jumlah_jam' => 8,
    ]);
});

it('forbids a pegawai from editing another pegawai webinar', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $webinar = Webinar::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('update', $webinar))->toBeFalse();
});

it('can delete an owned webinar from the table', function () {
    $pegawai = Pegawai::factory()->create();
    $webinar = Webinar::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListWebinars::class)
        ->callAction(TestAction::make('delete')->table($webinar))
        ->assertNotified();

    assertDatabaseMissing($webinar);
});

it('forbids a pegawai from deleting another pegawai webinar', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $webinar = Webinar::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('delete', $webinar))->toBeFalse();
});
