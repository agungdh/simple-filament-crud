<?php

use App\Enums\KategoriKebutuhanJamPelatihan;
use App\Enums\StatusPegawai;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\DashboardAdmin;
use App\Filament\Resources\BelajarMandiris\BelajarMandiriResource;
use App\Filament\Resources\Bidangs\BidangResource;
use App\Filament\Resources\Coachings\CoachingResource;
use App\Filament\Resources\Diklats\DiklatResource;
use App\Filament\Resources\JenisPelatihans\JenisPelatihanResource;
use App\Filament\Resources\Lcs\LcResource;
use App\Filament\Resources\MateriPpms\MateriPpmResource;
use App\Filament\Resources\Mentorings\MentoringResource;
use App\Filament\Resources\PangkatGolongans\PangkatGolonganResource;
use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Filament\Resources\Ppms\PpmResource;
use App\Filament\Resources\Seminars\SeminarResource;
use App\Filament\Resources\Webinars\WebinarResource;
use App\Filament\Resources\Workshops\WorkshopResource;
use App\Filament\Widgets\AdminRekapitulasiTable;
use App\Filament\Widgets\AdminStatsOverview;
use App\Filament\Widgets\PegawaiCapaianNotice;
use App\Filament\Widgets\PegawaiDetailPelatihan;
use App\Filament\Widgets\PegawaiRekapitulasi;
use App\Filament\Widgets\PegawaiStatsOverview;
use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\Ppm;
use App\Models\Seminar;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Filament::setCurrentPanel('admin');

    $this->seed(RoleSeeder::class);
});

it('orders the navigation groups like the legacy application', function () {
    $groups = Filament::getCurrentPanel()->getNavigationGroups();

    expect(array_map(fn ($group): string => $group->getLabel(), $groups))
        ->toBe(['Data Master', 'Kartu Kontrol', 'Pengembangan', 'Lainnya']);
});

it('orders the navigation items like the legacy application', function () {
    expect(PegawaiResource::getNavigationSort())->toBe(1)
        ->and(BidangResource::getNavigationSort())->toBe(2)
        ->and(JenisPelatihanResource::getNavigationSort())->toBe(3)
        ->and(PangkatGolonganResource::getNavigationSort())->toBe(4)
        ->and(DiklatResource::getNavigationSort())->toBe(1)
        ->and(PpmResource::getNavigationSort())->toBe(2)
        ->and(MateriPpmResource::getNavigationSort())->toBe(3)
        ->and(SeminarResource::getNavigationSort())->toBe(1)
        ->and(WebinarResource::getNavigationSort())->toBe(2)
        ->and(LcResource::getNavigationSort())->toBe(3)
        ->and(BelajarMandiriResource::getNavigationSort())->toBe(1)
        ->and(MentoringResource::getNavigationSort())->toBe(2)
        ->and(CoachingResource::getNavigationSort())->toBe(3)
        ->and(WorkshopResource::getNavigationSort())->toBe(4);
});

it('shows both dashboards to an admin', function () {
    actingAs(User::factory()->admin()->create());

    $this->get(Dashboard::getUrl())->assertOk();
    $this->get(DashboardAdmin::getUrl())->assertOk();
});

it('forbids a pegawai from accessing the admin dashboard', function () {
    actingAs(User::factory()->pegawai()->create());

    $this->get(DashboardAdmin::getUrl())->assertForbidden();
});

it('shows the pegawai jam pelatihan totals', function () {
    $pegawai = Pegawai::factory()->create();

    Diklat::factory()->create([
        'pegawai_id' => $pegawai->id,
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
        'jumlah_jam_pelatihan' => 10,
    ]);
    Seminar::factory()->create([
        'pegawai_id' => $pegawai->id,
        'tanggal_pelaksanaan' => now()->startOfYear(),
        'jumlah_jam' => 5,
    ]);

    actingAs($pegawai->user);

    Livewire::test(PegawaiStatsOverview::class)
        ->assertSee('Jumlah Jam Diklat')
        ->assertSee('10')
        ->assertSee('Jumlah Jam PPM')
        ->assertSee('Jumlah Jam Lain-Lain')
        ->assertSee('5');
});

it('warns a pegawai that has not reached the minimum jam pelatihan', function () {
    $pegawai = Pegawai::factory()->create([
        'kategori_kebutuhan_jam_pelatihan' => KategoriKebutuhanJamPelatihan::Admin,
    ]);

    Diklat::factory()->create([
        'pegawai_id' => $pegawai->id,
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
        'jumlah_jam_pelatihan' => 10,
    ]);

    actingAs($pegawai->user);

    Livewire::test(PegawaiCapaianNotice::class)
        ->assertSee('Perhatian!')
        ->assertSee('10')
        ->assertSee('20');
});

it('congratulates a pegawai that has reached the minimum jam pelatihan', function () {
    $pegawai = Pegawai::factory()->create([
        'kategori_kebutuhan_jam_pelatihan' => KategoriKebutuhanJamPelatihan::Admin,
    ]);

    Diklat::factory()->create([
        'pegawai_id' => $pegawai->id,
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
        'jumlah_jam_pelatihan' => 20,
    ]);

    actingAs($pegawai->user);

    Livewire::test(PegawaiCapaianNotice::class)
        ->assertSee('Selamat!');
});

it('shows the pegawai quarterly recap', function () {
    $pegawai = Pegawai::factory()->create();

    Diklat::factory()->create([
        'pegawai_id' => $pegawai->id,
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
        'jumlah_jam_pelatihan' => 7,
    ]);
    Ppm::factory()->create([
        'pegawai_id' => $pegawai->id,
        'tanggal_pelaksanaan' => now()->startOfYear()->addMonths(3),
        'jumlah_jam_pelatihan' => 3,
    ]);

    actingAs($pegawai->user);

    Livewire::test(PegawaiRekapitulasi::class)
        ->assertSee('Rekapitulasi Pegawai')
        ->assertSee('Triwulan I')
        ->assertSee('Triwulan II')
        ->assertSee('Triwulan III')
        ->assertSee('Triwulan IV')
        ->assertSee('Total JP');
});

it('only shows the authenticated pegawai training details', function () {
    $pegawai = Pegawai::factory()->create();
    $otherPegawai = Pegawai::factory()->create();

    Diklat::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Materi Milik Sendiri',
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
    ]);
    Diklat::factory()->create([
        'pegawai_id' => $otherPegawai->id,
        'materi_pengembangan' => 'Materi Milik Orang Lain',
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
    ]);

    actingAs($pegawai->user);

    Livewire::test(PegawaiDetailPelatihan::class)
        ->assertSee('Materi Milik Sendiri')
        ->assertDontSee('Materi Milik Orang Lain');
});

it('filters the pegawai training details by module', function () {
    $pegawai = Pegawai::factory()->create();

    Diklat::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Materi Diklat',
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
    ]);
    Seminar::factory()->create([
        'pegawai_id' => $pegawai->id,
        'materi_pengembangan' => 'Materi Seminar',
        'tanggal_pelaksanaan' => now()->startOfYear(),
    ]);

    actingAs($pegawai->user);

    Livewire::test(PegawaiDetailPelatihan::class, [
        'pageFilters' => [
            'tahun' => now()->year,
            'modul' => 'diklat',
        ],
    ])
        ->assertSee('Materi Diklat')
        ->assertDontSee('Materi Seminar');
});

it('shows the active pegawai recap to an admin', function () {
    $activePegawai = Pegawai::factory()->create();
    $inactivePegawai = Pegawai::factory()->create([
        'status' => StatusPegawai::NonAktif,
    ]);

    actingAs(User::factory()->admin()->create());

    Livewire::test(AdminRekapitulasiTable::class)
        ->assertCanSeeTableRecords([$activePegawai])
        ->assertCanNotSeeTableRecords([$inactivePegawai]);
});

it('shows the admin quarterly recap values', function () {
    $pegawai = Pegawai::factory()->create();

    Diklat::factory()->create([
        'pegawai_id' => $pegawai->id,
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
        'jumlah_jam_pelatihan' => 7,
    ]);
    Ppm::factory()->create([
        'pegawai_id' => $pegawai->id,
        'tanggal_pelaksanaan' => now()->startOfYear()->addMonths(3),
        'jumlah_jam_pelatihan' => 3,
    ]);

    actingAs(User::factory()->admin()->create());

    Livewire::test(AdminRekapitulasiTable::class)
        ->assertTableColumnStateSet('triwulan_1', 7, $pegawai)
        ->assertTableColumnStateSet('triwulan_2', 3, $pegawai)
        ->assertTableColumnStateSet('total_jp', 10, $pegawai);
});

it('filters the admin quarterly recap by module', function () {
    $pegawai = Pegawai::factory()->create();

    Diklat::factory()->create([
        'pegawai_id' => $pegawai->id,
        'dari_tanggal_pelaksanaan' => now()->startOfYear(),
        'jumlah_jam_pelatihan' => 7,
    ]);
    Ppm::factory()->create([
        'pegawai_id' => $pegawai->id,
        'tanggal_pelaksanaan' => now()->startOfYear()->addMonths(3),
        'jumlah_jam_pelatihan' => 3,
    ]);

    actingAs(User::factory()->admin()->create());

    Livewire::test(AdminRekapitulasiTable::class, [
        'pageFilters' => [
            'tahun' => now()->year,
            'modul' => 'diklat',
        ],
    ])
        ->assertTableColumnStateSet('triwulan_1', 7, $pegawai)
        ->assertTableColumnStateSet('triwulan_2', 0, $pegawai)
        ->assertTableColumnStateSet('total_jp', 7, $pegawai);
});

it('shows the admin statistics', function () {
    Pegawai::factory()->create();
    Pegawai::factory()->create([
        'status' => StatusPegawai::NonAktif,
    ]);

    actingAs(User::factory()->admin()->create());

    Livewire::test(AdminStatsOverview::class)
        ->assertSee('Jumlah Pegawai')
        ->assertSee('Jumlah Jam Diklat')
        ->assertSee('Jumlah Jam PPM')
        ->assertSee('Jumlah Jam Lain-Lain');
});
