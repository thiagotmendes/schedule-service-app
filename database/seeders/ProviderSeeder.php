<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Provider;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                $user->assignRole('provider');

                Provider::create([
                    'user_id' => $user->id,
                    'name'    => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'phone' => fake()->phoneNumber,
                    'document' => fake()->numerify('###########'),
                ]);
            });
    }
}
