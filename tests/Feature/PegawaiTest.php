<?php

use App\Enums\KategoriJabatan;
use App\Enums\KategoriKebutuhanJamPelatihan;
use App\Enums\StatusPegawai;
use App\Filament\Resources\Pegawais\Pages\CreatePegawai;
use App\Filament\Resources\Pegawais\Pages\EditPegawai;
use App\Filament\Resources\Pegawais\Pages\ListPegawais;
use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Models\Bidang;
use App\Models\PangkatGolongan;
use App\Models\Pegawai;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    Filament::setCurrentPanel('admin');

    $this->seed(RoleSeeder::class);

    actingAs(User::factory()->admin()->create());
});

it('can list pegawais', function () {
    $pegawais = Pegawai::factory()->count(3)->create();

    Livewire::test(ListPegawais::class)
        ->assertOk()
        ->assertCanSeeTableRecords($pegawais);
});

it('does not link table rows to the edit page', function () {
    $pegawai = Pegawai::factory()->create();

    $table = Livewire::test(ListPegawais::class)
        ->instance()
        ->getTable();

    expect($table->getRecordUrl($pegawai))->toBeNull()
        ->and($table->getRecordAction($pegawai))->toBeNull();
});

it('can filter pegawais by bidang', function () {
    $bidang = Bidang::factory()->create();
    $matchingPegawai = Pegawai::factory()->create([
        'bidang_id' => $bidang->id,
    ]);
    $otherPegawai = Pegawai::factory()->create();

    Livewire::test(ListPegawais::class)
        ->filterTable('bidang_id', $bidang->id)
        ->assertCanSeeTableRecords([$matchingPegawai])
        ->assertCanNotSeeTableRecords([$otherPegawai]);
});

it('can filter pegawais by role', function () {
    $adminPegawai = Pegawai::factory()->admin()->create();
    $pegawai = Pegawai::factory()->create();

    Livewire::test(ListPegawais::class)
        ->filterTable('role', 'admin')
        ->assertCanSeeTableRecords([$adminPegawai])
        ->assertCanNotSeeTableRecords([$pegawai]);
});

it('can filter pegawais by status', function () {
    $activePegawai = Pegawai::factory()->create([
        'status' => StatusPegawai::Aktif,
    ]);
    $inactivePegawai = Pegawai::factory()->create([
        'status' => StatusPegawai::NonAktif,
    ]);

    Livewire::test(ListPegawais::class)
        ->filterTable('status', StatusPegawai::NonAktif->value)
        ->assertCanSeeTableRecords([$inactivePegawai])
        ->assertCanNotSeeTableRecords([$activePegawai]);
});

it('can create a pegawai with a user account', function () {
    $bidang = Bidang::factory()->create();
    $pangkat = PangkatGolongan::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->assertActionDoesNotExist('createAnother')
        ->fillForm([
            'nip' => '198501012010011001',
            'nama' => 'Budi Santoso',
            'role' => 'pegawai',
            'status' => StatusPegawai::Aktif,
            'bidang_id' => $bidang->id,
            'pangkat_golongan_id' => $pangkat->id,
            'jabatan' => 'Auditor Muda',
            'peran' => 'Subkoordinator',
            'kategori_jabatan' => KategoriJabatan::FungsionalAuditor,
            'kategori_kebutuhan_jam_pelatihan' => KategoriKebutuhanJamPelatihan::Auditor,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(PegawaiResource::getUrl('index'));

    $user = User::query()->where('username', '198501012010011001')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('pegawai'))->toBeTrue()
        ->and(Hash::check('rahasia123', $user->password))->toBeTrue();

    assertDatabaseHas(Pegawai::class, [
        'user_id' => $user->id,
        'nip' => '198501012010011001',
        'nama' => 'Budi Santoso',
        'bidang_id' => $bidang->id,
        'pangkat_golongan_id' => $pangkat->id,
        'status' => StatusPegawai::Aktif->value,
        'jabatan' => 'Auditor Muda',
        'kategori_jabatan' => KategoriJabatan::FungsionalAuditor->value,
        'peran' => 'Subkoordinator',
        'kategori_kebutuhan_jam_pelatihan' => KategoriKebutuhanJamPelatihan::Auditor->value,
    ]);
});

it('can create an admin pegawai', function () {
    $bidang = Bidang::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->fillForm([
            'nip' => '198501012010011002',
            'nama' => 'Admin Baru',
            'role' => 'admin',
            'status' => StatusPegawai::Aktif,
            'bidang_id' => $bidang->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::query()->where('username', '198501012010011002')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('admin'))->toBeTrue();
});

it('requires the pegawai fields', function (string $field) {
    $bidang = Bidang::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->fillForm([
            'nip' => '198501012010011001',
            'nama' => 'Budi Santoso',
            'role' => 'pegawai',
            'status' => StatusPegawai::Aktif,
            'bidang_id' => $bidang->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            $field => null,
        ])
        ->call('create')
        ->assertHasFormErrors([$field => 'required'])
        ->assertNotNotified();
})->with(['nama', 'nip', 'role', 'status', 'bidang_id']);

it('requires a password when creating a pegawai', function () {
    $bidang = Bidang::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->fillForm([
            'nip' => '198501012010011001',
            'nama' => 'Budi Santoso',
            'role' => 'pegawai',
            'status' => StatusPegawai::Aktif,
            'bidang_id' => $bidang->id,
            'password' => null,
            'password_confirmation' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['password' => 'required'])
        ->assertNotNotified();
});

it('requires a unique nip', function () {
    $pegawai = Pegawai::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->fillForm([
            'nip' => $pegawai->nip,
            'nama' => 'Budi Santoso',
            'role' => 'pegawai',
            'status' => StatusPegawai::Aktif,
            'bidang_id' => $pegawai->bidang_id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])
        ->call('create')
        ->assertHasFormErrors(['nip' => 'unique'])
        ->assertNotNotified();
});

it('requires a nip that is not already used as a username', function () {
    $user = User::factory()->create([
        'username' => '198501012010011001',
    ]);
    $bidang = Bidang::factory()->create();

    Livewire::test(CreatePegawai::class)
        ->fillForm([
            'nip' => $user->username,
            'nama' => 'Budi Santoso',
            'role' => 'pegawai',
            'status' => StatusPegawai::Aktif,
            'bidang_id' => $bidang->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])
        ->call('create')
        ->assertHasFormErrors(['nip' => 'unique'])
        ->assertNotNotified();
});

it('can edit a pegawai and sync the user account', function () {
    $pegawai = Pegawai::factory()->create([
        'nama' => 'Budi',
    ]);
    $bidang = Bidang::factory()->create();

    Livewire::test(EditPegawai::class, [
        'record' => $pegawai->getRouteKey(),
    ])
        ->fillForm([
            'nip' => '198501012010011099',
            'nama' => 'Budi Santoso',
            'role' => 'admin',
            'status' => StatusPegawai::NonAktif,
            'bidang_id' => $bidang->id,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(PegawaiResource::getUrl('index'));

    $pegawai->refresh();

    expect($pegawai->user->username)->toBe('198501012010011099')
        ->and($pegawai->user->hasRole('admin'))->toBeTrue();

    assertDatabaseHas(Pegawai::class, [
        'id' => $pegawai->id,
        'nama' => 'Budi Santoso',
        'bidang_id' => $bidang->id,
        'status' => StatusPegawai::NonAktif->value,
    ]);
});

it('can update a pegawai password', function () {
    $pegawai = Pegawai::factory()->create();

    Livewire::test(EditPegawai::class, [
        'record' => $pegawai->getRouteKey(),
    ])
        ->fillForm([
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('passwordbaru', $pegawai->user->fresh()->password))->toBeTrue();
});

it('keeps the current password when the edit form leaves it blank', function () {
    $pegawai = Pegawai::factory()->create();
    $oldPassword = $pegawai->user->password;

    Livewire::test(EditPegawai::class, [
        'record' => $pegawai->getRouteKey(),
    ])
        ->fillForm([
            'password' => '',
            'password_confirmation' => '',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($pegawai->user->fresh()->password)->toBe($oldPassword);
});

it('can delete a pegawai from the table and its user account', function () {
    $pegawai = Pegawai::factory()->create();
    $user = $pegawai->user;

    Livewire::test(ListPegawais::class)
        ->callAction(TestAction::make('delete')->table($pegawai))
        ->assertNotified();

    assertDatabaseMissing($pegawai);
    assertDatabaseMissing($user);
});

it('cannot delete the currently authenticated pegawai', function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin);

    $pegawai = Pegawai::factory()->create([
        'user_id' => $admin->id,
    ]);

    Livewire::test(ListPegawais::class)
        ->assertActionDisabled(TestAction::make('delete')->table($pegawai));

    assertDatabaseHas(Pegawai::class, [
        'id' => $pegawai->id,
    ]);
});

it('forbids the pegawai role from accessing the pegawai resource', function () {
    actingAs(User::factory()->pegawai()->create());

    $this->get(PegawaiResource::getUrl('index'))->assertForbidden();
});
