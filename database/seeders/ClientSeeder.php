<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) {
                $user->assignRole('client');

                Client::create([
                    'user_id' => $user->id,
                    'name'    => fake()->name(),
                    'email'   => fake()->unique()->safeEmail(),
                    'phone' => fake()->phoneNumber,
                    'document' => fake()->numerify('###########'),
                ]);
            });
    }
}
