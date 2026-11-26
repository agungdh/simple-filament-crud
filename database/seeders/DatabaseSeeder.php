<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(BidangSeeder::class);
        $this->call(PangkatGolonganSeeder::class);
        $this->call(PegawaiSeeder::class);
        $this->call(JenisPelatihanSeeder::class);
        $this->call(DiklatSeeder::class);
        $this->call(PpmSeeder::class);
        $this->call(MateriPpmSeeder::class);
        $this->call(SeminarSeeder::class);
        $this->call(WebinarSeeder::class);
        $this->call(LcSeeder::class);
        $this->call(BelajarMandiriSeeder::class);
        $this->call(MentoringSeeder::class);
        $this->call(CoachingSeeder::class);
        $this->call(WorkshopSeeder::class);
    }
}
