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

it('renders a curated nature wallpaper on the login page', function () {
    config([
        'login.wallpaper.enabled' => true,
        'login.wallpaper.url' => null,
        'login.wallpaper.provider' => 'https://picsum.photos',
        'login.wallpaper.blur' => 2,
        'login.wallpaper.ids' => [10, 29, 128],
    ]);

    $response = $this->get('/admin/login')
        ->assertOk()
        ->assertSee('fi-login-wallpaper', false);

    preg_match("/--login-wallpaper-url: url\\('([^']+)'\\)/", $response->getContent(), $match);

    $wallpaperUrl = $match[1] ?? null;

    expect($wallpaperUrl)
        ->not->toBeNull()
        ->toMatch('/^https:\/\/picsum\.photos\/id\/\d+\/1920\/1080\?blur=2$/');

    preg_match('/\/id\/(\d+)\//', $wallpaperUrl, $idMatch);

    expect([10, 29, 128])->toContain((int) ($idMatch[1] ?? 0));
});

it('can disable the login wallpaper', function () {
    config(['login.wallpaper.enabled' => false]);

    $this->get('/admin/login')
        ->assertOk()
        ->assertDontSee('fi-login-wallpaper', false);
});
