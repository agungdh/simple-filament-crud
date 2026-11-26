<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Workshop;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
    /**
     * Seed the application's workshop data.
     */
    public function run(): void
    {
        if (Workshop::query()->exists()) {
            return;
        }

        Pegawai::query()->each(function (Pegawai $pegawai): void {
            Workshop::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
