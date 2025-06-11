<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test retrieving all clients.
     */
    public function test_can_get_all_clients()
    {
        // Create some clients
        Client::factory()->count(3)->create();

        // Make request to index endpoint
        $response = $this->getJson('/api/clients');

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test creating a new client.
     */
    public function test_can_create_client()
    {
        $clientData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
        ];

        $response = $this->postJson('/api/clients', $clientData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'message' => 'Client created successfully',
                     'data' => $clientData
                 ]);

        $this->assertDatabaseHas('clients', [
            'email' => $clientData['email']
        ]);
    }

    /**
     * Test validation when creating a client.
     */
    public function test_client_creation_requires_valid_data()
    {
        // Missing required fields
        $response = $this->postJson('/api/clients', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'phone']);

        // Invalid email
        $response = $this->postJson('/api/clients', [
            'name' => $this->faker->name,
            'email' => 'not-an-email',
            'phone' => $this->faker->phoneNumber,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test retrieving a specific client.
     */
    public function test_can_get_single_client()
    {
        $client = Client::factory()->create();

        $response = $this->getJson('/api/clients/' . $client->id);

        $response->assertStatus(200)
                 ->assertJson($client->toArray());
    }

    /**
     * Test updating a client.
     */
    public function test_can_update_client()
    {
        $client = Client::factory()->create();

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '555-1234',
        ];

        $response = $this->putJson('/api/clients/' . $client->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Client updated successfully'
                 ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test validation when updating a client.
     */
    public function test_client_update_requires_valid_data()
    {
        $client = Client::factory()->create();

        // Missing required fields
        $response = $this->putJson('/api/clients/' . $client->id, []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'phone']);
    }

    /**
     * Test deleting a client.
     */
    public function test_can_delete_client()
    {
        $client = Client::factory()->create();

        $response = $this->deleteJson('/api/clients/' . $client->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Client deleted successfully'
                 ]);

        // Since the model uses soft deletes, check that it's soft deleted
        $this->assertSoftDeleted('clients', [
            'id' => $client->id
        ]);
    }

    /**
     * Test error when client not found.
     */
    public function test_returns_404_when_client_not_found()
    {
        $response = $this->getJson('/api/clients/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/clients/999', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'phone' => '123-456-7890'
        ]);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/clients/999');
        $response->assertStatus(404);
    }
}
