<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Webinar;
use Illuminate\Database\Seeder;

class WebinarSeeder extends Seeder
{
    /**
     * Seed the application's webinar data.
     */
    public function run(): void
    {
        if (Webinar::query()->exists()) {
            return;
        }

        Pegawai::query()->each(function (Pegawai $pegawai): void {
            Webinar::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
