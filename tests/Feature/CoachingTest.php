<?php

use App\Filament\Resources\Coachings\CoachingResource;
use App\Filament\Resources\Coachings\Pages\CreateCoaching;
use App\Filament\Resources\Coachings\Pages\EditCoaching;
use App\Filament\Resources\Coachings\Pages\ListCoachings;
use App\Models\Coaching;
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

it('allows an admin to list all coachings', function () {
    actingAs(User::factory()->admin()->create());

    $coachings = Coaching::factory()->count(3)->create();

    Livewire::test(ListCoachings::class)
        ->assertOk()
        ->assertCanSeeTableRecords($coachings);
});

it('only lists the coachings owned by the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();

    $own = Coaching::factory()->create(['pegawai_id' => $pegawai->id]);
    $others = Coaching::factory()->create(['pegawai_id' => $otherPegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListCoachings::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$others]);
});

it('only shows the pegawai column to admins', function () {
    $pegawai = Pegawai::factory()->create();
    Coaching::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListCoachings::class)
        ->assertTableColumnHidden('pegawai.nama');

    actingAs(User::factory()->admin()->create());

    Livewire::test(ListCoachings::class)
        ->assertTableColumnVisible('pegawai.nama');
});

it('does not link table rows to the edit page', function () {
    actingAs(User::factory()->admin()->create());

    $coaching = Coaching::factory()->create();

    $table = Livewire::test(ListCoachings::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($coaching))->toBeNull()
        ->and($table->getRecordAction($coaching))->toBeNull();
});

it('creates a coaching for the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();

    actingAs($pegawai->user);

    Livewire::test(CreateCoaching::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'materi_pengembangan' => 'Coaching Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(CoachingResource::getUrl('index'));

    assertDatabaseHas(Coaching::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Coaching Laravel',
        'jumlah_jam' => 4,
    ]);
});

it('allows an admin to create a coaching for another pegawai', function () {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateCoaching::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Coaching Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Coaching::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Coaching Laravel',
    ]);
});

it('requires the coaching fields', function (string $field) {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateCoaching::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Coaching Laravel',
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

it('can edit an owned coaching', function () {
    $pegawai = Pegawai::factory()->create();
    $coaching = Coaching::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Coaching Lama',
    ]);

    actingAs($pegawai->user);

    Livewire::test(EditCoaching::class, [
        'record' => $coaching->getRouteKey(),
    ])
        ->fillForm([
            'materi_pengembangan' => 'Coaching Baru',
            'jumlah_jam' => 8,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(CoachingResource::getUrl('index'));

    assertDatabaseHas(Coaching::class, [
        'id' => $coaching->id,
        'materi_pengembangan' => 'Coaching Baru',
        'jumlah_jam' => 8,
    ]);
});

it('forbids a pegawai from editing another pegawai coaching', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $coaching = Coaching::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('update', $coaching))->toBeFalse();
});

it('can delete an owned coaching from the table', function () {
    $pegawai = Pegawai::factory()->create();
    $coaching = Coaching::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListCoachings::class)
        ->callAction(TestAction::make('delete')->table($coaching))
        ->assertNotified();

    assertDatabaseMissing($coaching);
});

it('forbids a pegawai from deleting another pegawai coaching', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $coaching = Coaching::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('delete', $coaching))->toBeFalse();
});
