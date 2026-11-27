<?php

use App\Filament\Resources\Mentorings\MentoringResource;
use App\Filament\Resources\Mentorings\Pages\CreateMentoring;
use App\Filament\Resources\Mentorings\Pages\EditMentoring;
use App\Filament\Resources\Mentorings\Pages\ListMentorings;
use App\Models\Mentoring;
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

it('allows an admin to list all mentorings', function () {
    actingAs(User::factory()->admin()->create());

    $mentorings = Mentoring::factory()->count(3)->create();

    Livewire::test(ListMentorings::class)
        ->assertOk()
        ->assertCanSeeTableRecords($mentorings);
});

it('only lists the mentorings owned by the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();

    $own = Mentoring::factory()->create(['pegawai_id' => $pegawai->id]);
    $others = Mentoring::factory()->create(['pegawai_id' => $otherPegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListMentorings::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$others]);
});

it('only shows the pegawai column to admins', function () {
    $pegawai = Pegawai::factory()->create();
    Mentoring::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListMentorings::class)
        ->assertTableColumnHidden('pegawai.nama');

    actingAs(User::factory()->admin()->create());

    Livewire::test(ListMentorings::class)
        ->assertTableColumnVisible('pegawai.nama');
});

it('does not link table rows to the edit page', function () {
    actingAs(User::factory()->admin()->create());

    $mentoring = Mentoring::factory()->create();

    $table = Livewire::test(ListMentorings::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($mentoring))->toBeNull()
        ->and($table->getRecordAction($mentoring))->toBeNull();
});

it('creates a mentoring for the authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->create();

    actingAs($pegawai->user);

    Livewire::test(CreateMentoring::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'materi_pengembangan' => 'Mentoring Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(MentoringResource::getUrl('index'));

    assertDatabaseHas(Mentoring::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Mentoring Laravel',
        'jumlah_jam' => 4,
    ]);
});

it('allows an admin to create a mentoring for another pegawai', function () {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateMentoring::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Mentoring Laravel',
            'tanggal_pelaksanaan' => '2026-04-01',
            'jumlah_jam' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Mentoring::class, [
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Mentoring Laravel',
    ]);
});

it('requires the mentoring fields', function (string $field) {
    actingAs(User::factory()->admin()->create());

    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreateMentoring::class)
        ->fillForm([
            'pegawai_id' => $pegawai->id,
            'materi_pengembangan' => 'Mentoring Laravel',
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

it('can edit an owned mentoring', function () {
    $pegawai = Pegawai::factory()->create();
    $mentoring = Mentoring::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Mentoring Lama',
    ]);

    actingAs($pegawai->user);

    Livewire::test(EditMentoring::class, [
        'record' => $mentoring->getRouteKey(),
    ])
        ->fillForm([
            'materi_pengembangan' => 'Mentoring Baru',
            'jumlah_jam' => 8,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(MentoringResource::getUrl('index'));

    assertDatabaseHas(Mentoring::class, [
        'id' => $mentoring->id,
        'materi_pengembangan' => 'Mentoring Baru',
        'jumlah_jam' => 8,
    ]);
});

it('forbids a pegawai from editing another pegawai mentoring', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $mentoring = Mentoring::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('update', $mentoring))->toBeFalse();
});

it('can delete an owned mentoring from the table', function () {
    $pegawai = Pegawai::factory()->create();
    $mentoring = Mentoring::factory()->create(['pegawai_id' => $pegawai->id]);

    actingAs($pegawai->user);

    Livewire::test(ListMentorings::class)
        ->callAction(TestAction::make('delete')->table($mentoring))
        ->assertNotified();

    assertDatabaseMissing($mentoring);
});

it('forbids a pegawai from deleting another pegawai mentoring', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();
    $mentoring = Mentoring::factory()->create(['pegawai_id' => $otherPegawai->id]);

    expect(Gate::forUser($pegawai->user)->allows('delete', $mentoring))->toBeFalse();
});
