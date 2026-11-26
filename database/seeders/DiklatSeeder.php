<?php

namespace Database\Seeders;

use App\Models\Diklat;
use App\Models\JenisPelatihan;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class DiklatSeeder extends Seeder
{
    /**
     * Seed the application's diklat data.
     */
    public function run(): void
    {
        if (Diklat::query()->exists()) {
            return;
        }

        $jenisPelatihanIds = JenisPelatihan::query()->pluck('id');

        Pegawai::query()->each(function (Pegawai $pegawai) use ($jenisPelatihanIds): void {
            Diklat::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
                'jenis_pelatihan_id' => $jenisPelatihanIds->isNotEmpty()
                    ? $jenisPelatihanIds->random()
                    : JenisPelatihan::factory(),
            ]);
        });
    }
}
