<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class FleetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'plate_number' => strtoupper(fake()->bothify('B #### ??')),
            'vehicle_type' => fake()->randomElement(['Truck', 'Van', 'Pickup']),
            'capacity' => fake()->numberBetween(1000, 5000),
            'driver_user_id' => User::factory(),
            'status' => 'available',
        ];
    }
}
