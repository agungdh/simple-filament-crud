<?php

namespace Database\Seeders;

use App\Models\Mentoring;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class MentoringSeeder extends Seeder
{
    /**
     * Seed the application's mentoring data.
     */
    public function run(): void
    {
        if (Mentoring::query()->exists()) {
            return;
        }

        Pegawai::query()->each(function (Pegawai $pegawai): void {
            Mentoring::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
