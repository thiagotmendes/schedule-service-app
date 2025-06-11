<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Support\Str;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $providers = Provider::with('services')->get();

        foreach (range(1, 300) as $_) {
            $provider = $providers->random();
            $service = $provider->services->random();

            Appointment::create([
                'client_id' => $clients->random()->id,
                'provider_id' => $provider->id,
                'service_id' => $service->id,
                'scheduled_at' => now()->addDays(rand(1, 30))->setTime(rand(8, 17), [0, 30][rand(0,1)]),
                'status' => collect(['pending', 'confirmed', 'cancelled', 'completed'])->random(),
                'notes' => Str::random(20)
            ]);
        }
    }
}
