<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Corte Masculino', 'description' => 'Corte de cabelo tradicional para homens', 'duration' => 30, 'price' => 40],
            ['name' => 'Corte Feminino', 'description' => 'Corte de cabelo feminino com finalização', 'duration' => 45, 'price' => 70],
            ['name' => 'Escova', 'description' => 'Alisamento temporário com secador', 'duration' => 45, 'price' => 50],
            ['name' => 'Progressiva', 'description' => 'Alisamento duradouro com produto químico', 'duration' => 120, 'price' => 250],
            ['name' => 'Barba Completa', 'description' => 'Modelagem e finalização da barba', 'duration' => 30, 'price' => 35],
            ['name' => 'Manicure', 'description' => 'Limpeza e esmaltação das unhas', 'duration' => 30, 'price' => 30],
            ['name' => 'Pedicure', 'description' => 'Limpeza e esmaltação dos pés', 'duration' => 30, 'price' => 35],
            ['name' => 'Design de Sobrancelha', 'description' => 'Modelagem com pinça e/ou cera', 'duration' => 20, 'price' => 25],
            ['name' => 'Depilação Cera Axilas', 'description' => 'Depilação com cera quente nas axilas', 'duration' => 20, 'price' => 25],
            ['name' => 'Massagem Relaxante', 'description' => 'Sessão de massagem relaxante corporal', 'duration' => 60, 'price' => 120],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
