<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test retrieving all appointments.
     */
    public function test_can_get_all_appointments()
    {
        // Create some appointments
        Appointment::factory()->count(3)->create();

        // Make request to index endpoint
        $response = $this->getJson('/api/appointments');

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test creating a new appointment.
     */
    public function test_can_create_appointment()
    {
        // Create related models
        $client = Client::factory()->create();
        $provider = Provider::factory()->create();
        $service = Service::factory()->create();

        $appointmentData = [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'scheduled_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'message' => 'Appointment created successfully',
                 ]);

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
        ]);
    }

    /**
     * Test validation when creating an appointment.
     */
    public function test_appointment_creation_requires_valid_data()
    {
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
            'scheduled_at' => now()->subDay()->format('Y-m-d H:i:s'), // Past date
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['scheduled_at']);
    }

    /**
     * Test retrieving a specific appointment.
     */
    public function test_can_get_single_appointment()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->getJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(200);

        // Get the response data
        $responseData = $response->json();

        // Assert that the appointment data matches (except for scheduled_at format)
        $this->assertEquals($appointment->id, $responseData['id']);
        $this->assertEquals($appointment->client_id, $responseData['client_id']);
        $this->assertEquals($appointment->provider_id, $responseData['provider_id']);
        $this->assertEquals($appointment->service_id, $responseData['service_id']);
        $this->assertEquals($appointment->status, $responseData['status']);
        $this->assertEquals($appointment->notes, $responseData['notes']);

        // For scheduled_at, just check that the date string contains the same date information
        $this->assertStringContainsString(
            $appointment->scheduled_at->format('Y-m-d H:i'),
            $responseData['scheduled_at']
        );
    }

    /**
     * Test updating an appointment.
     */
    public function test_can_update_appointment()
    {
        $appointment = Appointment::factory()->create();
        $newClient = Client::factory()->create();
        $newProvider = Provider::factory()->create();
        $newService = Service::factory()->create();

        $updatedData = [
            'client_id' => $newClient->id,
            'provider_id' => $newProvider->id,
            'service_id' => $newService->id,
            'scheduled_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ];

        $response = $this->putJson('/api/appointments/' . $appointment->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Appointment updated successfully'
                 ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'client_id' => $newClient->id,
            'provider_id' => $newProvider->id,
            'service_id' => $newService->id,
        ]);
    }

    /**
     * Test validation when updating an appointment.
     */
    public function test_appointment_update_requires_valid_data()
    {
        $appointment = Appointment::factory()->create();

        // Missing required fields
        $response = $this->putJson('/api/appointments/' . $appointment->id, []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['client_id', 'provider_id', 'service_id', 'scheduled_at']);
    }

    /**
     * Test deleting an appointment.
     */
    public function test_can_delete_appointment()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->deleteJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Appointment deleted successfully'
                 ]);

        // Since the model uses soft deletes, check that it's soft deleted
        $this->assertSoftDeleted('appointments', [
            'id' => $appointment->id
        ]);
    }

    /**
     * Test error when appointment not found.
     */
    public function test_returns_404_when_appointment_not_found()
    {
        $response = $this->getJson('/api/appointments/999');
        $response->assertStatus(404);

        $client = Client::factory()->create();
        $provider = Provider::factory()->create();
        $service = Service::factory()->create();

        $response = $this->putJson('/api/appointments/999', [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'scheduled_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ]);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/appointments/999');
        $response->assertStatus(404);
    }
}
