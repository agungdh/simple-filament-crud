<?php

use App\Filament\Pages\Auth\Login;
use App\Models\Pegawai;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticatedAs;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
});

it('authenticates using username and password', function () {
    $user = User::factory()->pegawai()->create([
        'username' => '198501012010011001',
    ]);

    Livewire::test(Login::class)
        ->fillForm([
            'username' => '198501012010011001',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    User::factory()->pegawai()->create([
        'username' => '198501012010011001',
    ]);

    Livewire::test(Login::class)
        ->fillForm([
            'username' => '198501012010011001',
            'password' => 'password-salah',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['username']);
});

it('allows admin and pegawai roles to access the panel', function () {
    $panel = Filament::getCurrentPanel();

    expect(User::factory()->admin()->create()->canAccessPanel($panel))->toBeTrue()
        ->and(User::factory()->pegawai()->create()->canAccessPanel($panel))->toBeTrue();
});

it('denies users without a role from accessing the panel', function () {
    expect(User::factory()->create()->canAccessPanel(Filament::getCurrentPanel()))->toBeFalse();
});

it('renders the dashboard for an authenticated pegawai', function () {
    $pegawai = Pegawai::factory()->admin()->create();

    actingAs($pegawai->user);

    $this->get('/admin')->assertOk();
});
