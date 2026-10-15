<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;


class TripFactory extends Factory
{

    public function definition(): array
    {
        return [
            'available_seats' => fake()->numberBetween(1, 20),
        ];
    }
}
