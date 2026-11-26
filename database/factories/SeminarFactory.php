<?php

namespace Database\Factories;

use App\Models\Pegawai;
use App\Models\Seminar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seminar>
 */
class SeminarFactory extends Factory
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
            'materi_pengembangan' => fake()->sentence(),
            'tanggal_pelaksanaan' => fake()->dateTimeBetween(now()->startOfYear(), now()->endOfYear()),
            'jumlah_jam' => fake()->numberBetween(1, 100),
            'berkas' => null,
            'filename' => null,
        ];
    }
}
