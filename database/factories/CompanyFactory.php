<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'brand' => $this->faker->sentence(3),
            'nif' => $this->faker->biasedNumberBetween(100000000, 999999999),
            'niss' => $this->faker->biasedNumberBetween(100000000, 999999999),
            'notes' => $this->faker->paragraph(10),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
