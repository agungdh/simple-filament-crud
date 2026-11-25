<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Seed the application's master bidang data.
     */
    public function run(): void
    {
        $bidangs = [
            'Sekretariat',
            'Keuangan',
            'Perencanaan',
            'Umum dan Kepegawaian',
            'Hukum',
            'Pengawasan',
            'Teknologi Informasi',
        ];

        foreach ($bidangs as $bidang) {
            Bidang::query()->firstOrCreate(['bidang' => $bidang]);
        }
    }
}
