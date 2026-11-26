<?php

namespace Database\Factories;

use App\Models\MateriPpm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MateriPpm>
 */
class MateriPpmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_surat' => fake()->regexify('[A-Z]{2}-[0-9]{3}/[A-Z]{3}/[0-9]{4}'),
            'nama_pemateri' => fake()->name(),
            'materi_pengembangan' => fake()->sentence(),
            'tanggal_pelaksanaan' => fake()->dateTimeBetween(now()->startOfYear(), now()->endOfYear()),
            'link_materi' => fake()->url(),
            'link_dokumentasi' => fake()->url(),
        ];
    }
}
