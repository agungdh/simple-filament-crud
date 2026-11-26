<?php

namespace Database\Factories;

use App\Models\Pegawai;
use App\Models\Ppm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ppm>
 */
class PpmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pegawai_id' => Pegawai::factory(),
            'nomor_surat' => fake()->unique()->numerify('PPM-###########'),
            'materi_pengembangan' => fake()->sentence(),
            'tanggal_pelaksanaan' => fake()->dateTimeBetween(now()->startOfYear(), now()->endOfYear()),
            'jumlah_jam_pelatihan' => fake()->numberBetween(1, 100),
        ];
    }
}
