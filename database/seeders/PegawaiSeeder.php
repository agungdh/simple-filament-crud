<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Seed the application's master pegawai data.
     */
    public function run(): void
    {
        $pegawais = [
            [
                'nama' => 'Budi Santoso',
                'nip' => '198501012010011001',
                'bidang' => 'Sekretariat',
            ],
            [
                'nama' => 'Siti Aminah',
                'nip' => '199002022015012002',
                'bidang' => 'Keuangan',
            ],
            [
                'nama' => 'Agus Pratama',
                'nip' => '198703032012011003',
                'bidang' => 'Perencanaan',
            ],
        ];

        foreach ($pegawais as $pegawai) {
            $bidang = Bidang::query()->firstOrCreate([
                'bidang' => $pegawai['bidang'],
            ]);

            Pegawai::query()->firstOrCreate([
                'nip' => $pegawai['nip'],
            ], [
                'nama' => $pegawai['nama'],
                'bidang_id' => $bidang->id,
            ]);
        }
    }
}
