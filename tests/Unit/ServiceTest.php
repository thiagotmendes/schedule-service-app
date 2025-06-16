<?php

namespace Tests\Unit;

use App\Models\Service;
use App\Models\Provider;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_has_correct_attributes()
    {
        $service = Service::factory()->create([
            'name' => 'Test Service',
            'description' => 'Test Description',
            'duration' => 60,
            'price' => 100.00
        ]);

        $this->assertEquals('Test Service', $service->name);
        $this->assertEquals('Test Description', $service->description);
        $this->assertEquals(60, $service->duration);
        $this->assertEquals(100.00, $service->price);
    }

    public function test_service_can_have_providers()
    {
        $service = Service::factory()->create();
        $provider = Provider::factory()->create();
        
        $service->providers()->attach($provider->id);
        
        $this->assertTrue($service->providers->contains($provider));
    }

    public function test_service_can_have_appointments()
    {
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create(['service_id' => $service->id]);

        $this->assertInstanceOf(Appointment::class, $service->appointments->first());
        $this->assertEquals($service->id, $appointment->service_id);
    }

    public function test_service_can_be_updated()
    {
        $service = Service::factory()->create([
            'name' => 'Original Name',
            'price' => 50.00
        ]);

        $service->update([
            'name' => 'Updated Name',
            'price' => 75.00
        ]);

        $this->assertEquals('Updated Name', $service->fresh()->name);
        $this->assertEquals(75.00, $service->fresh()->price);
    }
} 