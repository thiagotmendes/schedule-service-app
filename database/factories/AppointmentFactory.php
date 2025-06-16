<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'provider_id' => Provider::factory(),
            'service_id' => Service::factory(),
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'notes' => fake()->optional(0.7)->sentence(),
        ];
    }
}
