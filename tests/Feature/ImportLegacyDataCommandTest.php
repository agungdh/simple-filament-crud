<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

it('imports the legacy dump', function () {
    $this->artisan('legacy:import', [
        'path' => 'tests/Fixtures/legacy-dump.sql',
        '--fresh' => true,
    ])->assertSuccessful();

    expect(DB::table('bidangs')->count())->toBe(2)
        ->and(DB::table('pangkat_golongans')->count())->toBe(1)
        ->and(DB::table('jenis_pelatihans')->count())->toBe(1)
        ->and(DB::table('users')->count())->toBe(2)
        ->and(DB::table('pegawais')->count())->toBe(2)
        ->and(DB::table('ppms')->count())->toBe(2)
        ->and(DB::table('diklats')->count())->toBe(1)
        ->and(DB::table('materi_ppms')->count())->toBe(1);
});

it('preserves ids and values from the dump', function () {
    $this->artisan('legacy:import', [
        'path' => 'tests/Fixtures/legacy-dump.sql',
        '--fresh' => true,
    ])->assertSuccessful();

    expect(DB::table('bidangs')->where('id', 1)->value('bidang'))->toBe('BIDANG PROGRAM; PELAPORAN')
        ->and(DB::table('bidangs')->where('id', 2)->value('bidang'))->toBe("BIDANG O'Brien")
        ->and(DB::table('pegawais')->where('id', 1)->value('nama'))->toBe("Admin O'Brien")
        ->and(DB::table('pegawais')->where('id', 1)->value('peran'))->toBe('Sekretaris; Kaper')
        ->and(DB::table('pegawais')->where('id', 2)->value('pangkat_golongan_id'))->toBeNull()
        ->and(DB::table('pegawais')->where('id', 2)->value('status'))->toBe('non aktif')
        ->and(DB::table('ppms')->where('id', 2)->value('materi_pengembangan'))->toBe("Materi 'kutip' ganda;")
        ->and(DB::table('diklats')->where('id', 1)->value('berkas'))->toBeNull()
        ->and(DB::table('materi_ppms')->where('id', 1)->value('nama_pemateri'))->toBe("Pemateri O'Neil");
});

it('assigns the legacy roles to the imported users', function () {
    $this->artisan('legacy:import', [
        'path' => 'tests/Fixtures/legacy-dump.sql',
        '--fresh' => true,
    ])->assertSuccessful();

    expect(User::find(1)?->hasRole('admin'))->toBeTrue()
        ->and(User::find(2)?->hasRole('pegawai'))->toBeTrue();
});

it('advances the sequences past the imported ids', function () {
    $this->artisan('legacy:import', [
        'path' => 'tests/Fixtures/legacy-dump.sql',
        '--fresh' => true,
    ])->assertSuccessful();

    $id = DB::table('bidangs')->insertGetId([
        'bidang' => 'Bidang Baru',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect($id)->toBe(3);
});

it('only reports the dump when running dry-run', function () {
    $this->artisan('legacy:import', [
        'path' => 'tests/Fixtures/legacy-dump.sql',
        '--dry-run' => true,
    ])->assertSuccessful();

    expect(DB::table('users')->count())->toBe(0)
        ->and(DB::table('pegawais')->count())->toBe(0);
});

it('refuses to import over existing data without --fresh', function () {
    User::factory()->create();

    $this->artisan('legacy:import', [
        'path' => 'tests/Fixtures/legacy-dump.sql',
    ])->assertFailed();

    expect(DB::table('users')->count())->toBe(1);
});

it('fails when the dump file does not exist', function () {
    $this->artisan('legacy:import', [
        'path' => 'tests/Fixtures/missing-dump.sql',
    ])->assertFailed();
});
