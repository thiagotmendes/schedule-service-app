<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\AuthenticatesUser;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, AuthenticatesUser;

    /**
     * Test retrieving all appointments.
     */
    public function test_can_get_all_appointments()
    {
        $this->authenticateUser();
        Appointment::factory()->count(3)->create();

        $response = $this->getJson('/api/appointments');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test creating a new appointment.
     */
    public function test_can_create_appointment()
    {
        $this->authenticateUser();
        $client = Client::factory()->create();
        $provider = Provider::factory()->create();
        $service = Service::factory()->create();

        $appointmentData = [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'pending',
            'notes' => $this->faker->sentence,
        ];

        $response = $this->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Appointment created successfully',
                     'data' => array_merge($appointmentData, [
                         'id' => 1,
                         'created_at' => $response->json('data.created_at'),
                         'updated_at' => $response->json('data.updated_at')
                     ])
                 ]);

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test validation when creating an appointment.
     */
    public function test_appointment_creation_requires_valid_data()
    {
        $this->authenticateUser();
        // Missing required fields
        $response = $this->postJson('/api/appointments', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['client_id', 'provider_id', 'service_id', 'scheduled_at']);

        // Invalid date (past date)
        $client = Client::factory()->create();
        $provider = Provider::factory()->create();
        $service = Service::factory()->create();

        $response = $this->postJson('/api/appointments', [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'scheduled_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['scheduled_at']);
    }

    /**
     * Test retrieving a specific appointment.
     */
    public function test_can_get_single_appointment()
    {
        $this->authenticateUser();
        $appointment = Appointment::factory()->create();

        $response = $this->getJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $appointment->id,
                     'client_id' => $appointment->client_id,
                     'provider_id' => $appointment->provider_id,
                     'service_id' => $appointment->service_id,
                     'scheduled_at' => $appointment->scheduled_at->format('Y-m-d H:i:s'),
                     'status' => $appointment->status,
                     'notes' => $appointment->notes,
                     'created_at' => $appointment->created_at->toJSON(),
                     'updated_at' => $appointment->updated_at->toJSON(),
                     'deleted_at' => null
                 ]);
    }

    /**
     * Test updating an appointment.
     */
    public function test_can_update_appointment()
    {
        $this->authenticateUser();
        $appointment = Appointment::factory()->create();
        $newProvider = Provider::factory()->create();
        $newService = Service::factory()->create();

        $updatedData = [
            'client_id' => $appointment->client_id,
            'provider_id' => $newProvider->id,
            'service_id' => $newService->id,
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'status' => 'confirmed',
            'notes' => 'Updated notes',
        ];

        $response = $this->putJson('/api/appointments/' . $appointment->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Appointment updated successfully'
                 ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'client_id' => $appointment->client_id,
            'provider_id' => $newProvider->id,
            'service_id' => $newService->id,
            'status' => 'confirmed',
            'notes' => 'Updated notes',
        ]);
    }

    /**
     * Test validation when updating an appointment.
     */
    public function test_appointment_update_requires_valid_data()
    {
        $this->authenticateUser();
        $appointment = Appointment::factory()->create();

        // Missing required fields
        $response = $this->putJson('/api/appointments/' . $appointment->id, []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['provider_id', 'service_id', 'scheduled_at']);

        // Invalid date (past date)
        $response = $this->putJson('/api/appointments/' . $appointment->id, [
            'provider_id' => $appointment->provider_id,
            'service_id' => $appointment->service_id,
            'scheduled_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['scheduled_at']);
    }

    /**
     * Test deleting an appointment.
     */
    public function test_can_delete_appointment()
    {
        $this->authenticateUser();
        $appointment = Appointment::factory()->create();

        $response = $this->deleteJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Appointment deleted successfully'
                 ]);

        $this->assertSoftDeleted('appointments', [
            'id' => $appointment->id
        ]);
    }

    /**
     * Test error when appointment not found.
     */
    public function test_returns_404_when_appointment_not_found()
    {
        $this->authenticateUser();
        $response = $this->getJson('/api/appointments/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/appointments/999', [
            'provider_id' => 1,
            'service_id' => 1,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/appointments/999');
        $response->assertStatus(404);
    }
}
