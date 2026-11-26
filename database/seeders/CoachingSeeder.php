<?php

namespace Database\Seeders;

use App\Models\Coaching;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class CoachingSeeder extends Seeder
{
    /**
     * Seed the application's coaching data.
     */
    public function run(): void
    {
        if (Coaching::query()->exists()) {
            return;
        }

        Pegawai::query()->each(function (Pegawai $pegawai): void {
            Coaching::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
