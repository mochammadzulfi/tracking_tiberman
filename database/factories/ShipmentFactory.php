<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Fleet;

class ShipmentFactory extends Factory
{
    public function definition(): array
    {
        $status = ['draft', 'assigned', 'on_progress', 'delivered', 'cancelled'];

        return [
            'code' => 'SJ/' . strtoupper(fake()->bothify('??')) . '/' . now()->format('Y') . '/' . fake()->numerify('#####'),
            'barcode_data' => fake()->uuid(),
            'customer_name' => fake()->company(),
            'origin_address' => fake()->address(),
            'destination_address' => fake()->address(),
            'weight' => fake()->randomFloat(2, 10, 500),
            'volume' => fake()->randomFloat(2, 1, 100),
            'status' => fake()->randomElement($status),
            'assigned_fleet_id' => Fleet::factory(),
            'assigned_driver_id' => User::factory(),
            'scheduled_at' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'created_by_user_id' => User::factory(),
        ];
    }
}
