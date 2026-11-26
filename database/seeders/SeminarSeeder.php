<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Seminar;
use Illuminate\Database\Seeder;

class SeminarSeeder extends Seeder
{
    /**
     * Seed the application's seminar data.
     */
    public function run(): void
    {
        if (Seminar::query()->exists()) {
            return;
        }

        Pegawai::query()->each(function (Pegawai $pegawai): void {
            Seminar::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
