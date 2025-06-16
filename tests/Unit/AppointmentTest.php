<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_belongs_to_client()
    {
        $client = Client::factory()->create();
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->assertInstanceOf(Client::class, $appointment->client);
        $this->assertEquals($client->id, $appointment->client->id);
    }

    public function test_appointment_belongs_to_provider()
    {
        $provider = Provider::factory()->create();
        $appointment = Appointment::factory()->create(['provider_id' => $provider->id]);

        $this->assertInstanceOf(Provider::class, $appointment->provider);
        $this->assertEquals($provider->id, $appointment->provider->id);
    }

    public function test_appointment_belongs_to_service()
    {
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create(['service_id' => $service->id]);

        $this->assertInstanceOf(Service::class, $appointment->service);
        $this->assertEquals($service->id, $appointment->service->id);
    }

    public function test_appointment_has_correct_status()
    {
        $appointment = Appointment::factory()->create(['status' => 'pending']);
        
        $this->assertEquals('pending', $appointment->status);
        
        $appointment->status = 'completed';
        $appointment->save();
        
        $this->assertEquals('completed', $appointment->fresh()->status);
    }

    public function test_appointment_has_correct_datetime()
    {
        $datetime = now();
        $appointment = Appointment::factory()->create(['scheduled_at' => $datetime]);
        
        $this->assertEquals($datetime->format('Y-m-d H:i:s'), $appointment->scheduled_at->format('Y-m-d H:i:s'));
    }
} 