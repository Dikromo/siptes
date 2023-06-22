<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'no_telp' => fake()->e164PhoneNumber,
            'perusahaan' => fake()->company(),
            'fileexcel_id' => mt_rand(1, 2),
            'status' => 0,
        ];
    }
}
