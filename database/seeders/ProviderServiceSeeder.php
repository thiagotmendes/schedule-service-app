<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provider;
use App\Models\Service;

class ProviderServiceSeeder extends Seeder
{
    public function run(): void
    {
        $providers = Provider::all();
        $services = Service::all();

        foreach ($providers as $provider) {
            $servicesToAttach = $services->random(rand(2, 5));

            $data = [];

            foreach ($servicesToAttach as $service) {
                $data[$service->id] = [
                    'price_override' => rand(50, 200),
                ];
            }

            $provider->services()->attach($data);
        }
    }
}
