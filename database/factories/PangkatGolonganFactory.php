<?php

namespace Database\Factories;

use App\Models\PangkatGolongan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PangkatGolongan>
 */
class PangkatGolonganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenjang' => fake()->randomElement(['Juru', 'Pengatur', 'Penata', 'Pembina', 'Pembina Utama']),
            'pangkat' => fake()->unique()->word(),
            'golongan' => fake()->randomElement(['I', 'II', 'III', 'IV']),
            'ruang' => fake()->randomElement(['a', 'b', 'c', 'd', 'e']),
        ];
    }
}
