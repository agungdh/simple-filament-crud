<?php

namespace Database\Seeders;

use App\Models\JenisPelatihan;
use Illuminate\Database\Seeder;

class JenisPelatihanSeeder extends Seeder
{
    /**
     * Seed the application's master jenis pelatihan data.
     */
    public function run(): void
    {
        $jenisPelatihans = [
            'Penjenjangan',
            'MOOC',
            'Sertifikasi',
            'Teknis Substansif',
            'Pelatihan Kepemimpinan',
        ];

        foreach ($jenisPelatihans as $jenisPelatihan) {
            JenisPelatihan::query()->firstOrCreate([
                'jenis_pelatihan' => $jenisPelatihan,
            ]);
        }
    }
}
