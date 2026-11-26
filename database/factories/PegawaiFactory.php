<?php

namespace Database\Factories;

use App\Enums\KategoriJabatan;
use App\Enums\KategoriKebutuhanJamPelatihan;
use App\Enums\StatusPegawai;
use App\Models\Bidang;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<Pegawai>
 */
class PegawaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nip = fake()->unique()->numerify('##################');

        return [
            'user_id' => User::factory()->pegawai()->state(['username' => $nip]),
            'nip' => $nip,
            'nama' => fake()->name(),
            'bidang_id' => Bidang::factory(),
            'pangkat_golongan_id' => null,
            'status' => StatusPegawai::Aktif,
            'jabatan' => fake()->jobTitle(),
            'kategori_jabatan' => fake()->randomElement(KategoriJabatan::cases()),
            'peran' => fake()->jobTitle(),
            'kategori_kebutuhan_jam_pelatihan' => fake()->randomElement(KategoriKebutuhanJamPelatihan::cases()),
        ];
    }

    /**
     * Indicate that the pegawai has the admin role.
     */
    public function admin(): static
    {
        return $this->afterCreating(function (Pegawai $pegawai): void {
            $pegawai->user->syncRoles([Role::findOrCreate('admin', 'web')]);
        });
    }
}
