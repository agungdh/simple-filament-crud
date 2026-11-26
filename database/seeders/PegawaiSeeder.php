<?php

namespace Database\Seeders;

use App\Enums\KategoriJabatan;
use App\Enums\KategoriKebutuhanJamPelatihan;
use App\Enums\StatusPegawai;
use App\Models\Bidang;
use App\Models\Pegawai;
use App\Models\User;
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
                'nip' => '198501012010011001',
                'nama' => 'Budi Santoso',
                'bidang' => 'Sekretariat',
                'role' => 'admin',
                'status' => StatusPegawai::Aktif,
                'jabatan' => 'Kepala Bagian Umum',
                'kategori_jabatan' => KategoriJabatan::Struktural,
                'peran' => 'Pejabat Struktural',
                'kategori_kebutuhan_jam_pelatihan' => KategoriKebutuhanJamPelatihan::Pejabat,
            ],
            [
                'nip' => '199002022015012002',
                'nama' => 'Siti Aminah',
                'bidang' => 'Keuangan',
                'role' => 'pegawai',
                'status' => StatusPegawai::Aktif,
                'jabatan' => 'Pengadministrasi Perkantoran',
                'kategori_jabatan' => KategoriJabatan::FungsionalTertentu,
                'peran' => 'Pramubhakti',
                'kategori_kebutuhan_jam_pelatihan' => KategoriKebutuhanJamPelatihan::Admin,
            ],
            [
                'nip' => '198703032012011003',
                'nama' => 'Agus Pratama',
                'bidang' => 'Perencanaan',
                'role' => 'pegawai',
                'status' => StatusPegawai::Aktif,
                'jabatan' => 'Auditor Muda',
                'kategori_jabatan' => KategoriJabatan::FungsionalAuditor,
                'peran' => 'Subkoordinator',
                'kategori_kebutuhan_jam_pelatihan' => KategoriKebutuhanJamPelatihan::Auditor,
            ],
        ];

        foreach ($pegawais as $pegawai) {
            $bidang = Bidang::query()->firstOrCreate([
                'bidang' => $pegawai['bidang'],
            ]);

            $user = User::query()->firstOrCreate([
                'username' => $pegawai['nip'],
            ], [
                'password' => $pegawai['nip'],
            ]);

            $user->syncRoles([$pegawai['role']]);

            Pegawai::query()->updateOrCreate([
                'nip' => $pegawai['nip'],
            ], [
                'user_id' => $user->id,
                'nama' => $pegawai['nama'],
                'bidang_id' => $bidang->id,
                'status' => $pegawai['status'],
                'jabatan' => $pegawai['jabatan'],
                'kategori_jabatan' => $pegawai['kategori_jabatan'],
                'peran' => $pegawai['peran'],
                'kategori_kebutuhan_jam_pelatihan' => $pegawai['kategori_kebutuhan_jam_pelatihan'],
            ]);
        }
    }
}
