<?php

namespace Database\Seeders;

use App\Models\Lc;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class LcSeeder extends Seeder
{
    /**
     * Seed the application's LC data.
     */
    public function run(): void
    {
        if (Lc::query()->exists()) {
            return;
        }

        Pegawai::query()->each(function (Pegawai $pegawai): void {
            Lc::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
