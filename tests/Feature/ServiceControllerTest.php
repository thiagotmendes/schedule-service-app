<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test retrieving all services.
     */
    public function test_can_get_all_services()
    {
        // Create some services
        Service::factory()->count(3)->create();

        // Make request to index endpoint
        $response = $this->getJson('/api/services');

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test creating a new service.
     */
    public function test_can_create_service()
    {
        $serviceData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'duration' => $this->faker->numberBetween(30, 120),
            'price' => $this->faker->randomFloat(2, 10, 200),
        ];

        $response = $this->postJson('/api/services', $serviceData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'message' => 'Service created successfully',
                     'data' => $serviceData
                 ]);

        $this->assertDatabaseHas('services', [
            'name' => $serviceData['name'],
            'description' => $serviceData['description']
        ]);
    }

    /**
     * Test validation when creating a service.
     */
    public function test_service_creation_requires_valid_data()
    {
        // Missing required fields
        $response = $this->postJson('/api/services', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'description', 'duration', 'price']);

        // Invalid duration (less than minimum)
        $response = $this->postJson('/api/services', [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'duration' => 0, // Invalid: less than min:1
            'price' => $this->faker->randomFloat(2, 10, 200),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['duration']);
    }

    /**
     * Test retrieving a specific service.
     */
    public function test_can_get_single_service()
    {
        $service = Service::factory()->create();

        $response = $this->getJson('/api/services/' . $service->id);

        $response->assertStatus(200)
                 ->assertJson($service->toArray());
    }

    /**
     * Test updating a service.
     */
    public function test_can_update_service()
    {
        $service = Service::factory()->create();

        $updatedData = [
            'name' => 'Updated Service',
            'description' => 'Updated description',
            'duration' => 60,
            'price' => 99.99,
        ];

        $response = $this->putJson('/api/services/' . $service->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Service updated successfully'
                 ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Service',
            'description' => 'Updated description',
            'duration' => 60,
            'price' => 99.99,
        ]);
    }

    /**
     * Test validation when updating a service.
     */
    public function test_service_update_requires_valid_data()
    {
        $service = Service::factory()->create();

        // Missing required fields
        $response = $this->putJson('/api/services/' . $service->id, []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'description', 'duration', 'price']);
    }

    /**
     * Test deleting a service.
     */
    public function test_can_delete_service()
    {
        $service = Service::factory()->create();

        $response = $this->deleteJson('/api/services/' . $service->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Service deleted successfully'
                 ]);

        $this->assertDatabaseMissing('services', [
            'id' => $service->id
        ]);
    }

    /**
     * Test error when service not found.
     */
    public function test_returns_404_when_service_not_found()
    {
        $response = $this->getJson('/api/services/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/services/999', [
            'name' => 'Test Service',
            'description' => 'Test description',
            'duration' => 45,
            'price' => 50.00
        ]);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/services/999');
        $response->assertStatus(404);
    }
}
