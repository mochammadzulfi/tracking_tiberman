<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\Group;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $roles = ['creator', 'admin', 'view_only', 'superuser'];

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'role' => fake()->randomElement($roles),
            'group_id' => Group::factory(),
        ];
    }
}
