<?php

namespace Database\Factories;

use App\Models\Mentoring;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mentoring>
 */
class MentoringFactory extends Factory
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
