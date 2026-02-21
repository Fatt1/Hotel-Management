<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                'role_id' => Role::factory(),
                'first_name' => "Admin",
                'last_name' => "User",
                'phone_number' => $this->faker->phoneNumber(),
                'email' => "admin@gmail.com", // Default email for testing
                'is_active' => true, // 80% chance of being active
                'password' => 'admin', // Default password for testing
        ];
    }
}
