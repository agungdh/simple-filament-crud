<?php

namespace Database\Seeders;

use App\Models\BelajarMandiri;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class BelajarMandiriSeeder extends Seeder
{
    /**
     * Seed the application's belajar mandiri data.
     */
    public function run(): void
    {
        if (BelajarMandiri::query()->exists()) {
            return;
        }

        Pegawai::query()->each(function (Pegawai $pegawai): void {
            BelajarMandiri::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
