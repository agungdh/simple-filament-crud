<?php

namespace Database\Seeders;

use App\Models\PangkatGolongan;
use Illuminate\Database\Seeder;

class PangkatGolonganSeeder extends Seeder
{
    /**
     * Seed the application's master pangkat golongan data.
     */
    public function run(): void
    {
        $pangkats = [
            ['jenjang' => 'Juru', 'pangkat' => 'Juru Muda', 'golongan' => 'I', 'ruang' => 'a'],
            ['jenjang' => 'Juru', 'pangkat' => 'Juru Muda Tingkat I', 'golongan' => 'I', 'ruang' => 'b'],
            ['jenjang' => 'Juru', 'pangkat' => 'Juru', 'golongan' => 'I', 'ruang' => 'c'],
            ['jenjang' => 'Juru', 'pangkat' => 'Juru Tingkat I', 'golongan' => 'I', 'ruang' => 'd'],
            ['jenjang' => 'Pengatur', 'pangkat' => 'Pengatur Muda', 'golongan' => 'II', 'ruang' => 'a'],
            ['jenjang' => 'Pengatur', 'pangkat' => 'Pengatur Muda Tingkat I', 'golongan' => 'II', 'ruang' => 'b'],
            ['jenjang' => 'Pengatur', 'pangkat' => 'Pengatur', 'golongan' => 'II', 'ruang' => 'c'],
            ['jenjang' => 'Pengatur', 'pangkat' => 'Pengatur Tingkat I', 'golongan' => 'II', 'ruang' => 'd'],
            ['jenjang' => 'Penata', 'pangkat' => 'Penata Muda', 'golongan' => 'III', 'ruang' => 'a'],
            ['jenjang' => 'Penata', 'pangkat' => 'Penata Muda Tingkat I', 'golongan' => 'III', 'ruang' => 'b'],
            ['jenjang' => 'Penata', 'pangkat' => 'Penata', 'golongan' => 'III', 'ruang' => 'c'],
            ['jenjang' => 'Penata', 'pangkat' => 'Penata Tingkat I', 'golongan' => 'III', 'ruang' => 'd'],
            ['jenjang' => 'Pembina', 'pangkat' => 'Pembina', 'golongan' => 'IV', 'ruang' => 'a'],
            ['jenjang' => 'Pembina', 'pangkat' => 'Pembina Tingkat I', 'golongan' => 'IV', 'ruang' => 'b'],
            ['jenjang' => 'Pembina Utama', 'pangkat' => 'Pembina Utama Muda', 'golongan' => 'IV', 'ruang' => 'c'],
            ['jenjang' => 'Pembina Utama', 'pangkat' => 'Pembina Utama Madya', 'golongan' => 'IV', 'ruang' => 'd'],
            ['jenjang' => 'Pembina Utama', 'pangkat' => 'Pembina Utama', 'golongan' => 'IV', 'ruang' => 'e'],
        ];

        foreach ($pangkats as $pangkat) {
            PangkatGolongan::query()->firstOrCreate([
                'pangkat' => $pangkat['pangkat'],
                'golongan' => $pangkat['golongan'],
                'ruang' => $pangkat['ruang'],
            ], [
                'jenjang' => $pangkat['jenjang'],
            ]);
        }
    }
}
