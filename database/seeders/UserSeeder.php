<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->assignRole('admin');

        // Criar 5 providers
        User::factory()
            ->count(5)
            ->create()
            ->each(fn ($user) => $user->assignRole('provider'));

        // Criar 10 clients
        User::factory()
            ->count(10)
            ->create()
            ->each(fn ($user) => $user->assignRole('client'));
    }
}
