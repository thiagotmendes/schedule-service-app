<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\AuthenticatesUser;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, AuthenticatesUser;

    /**
     * Test retrieving all clients.
     */
    public function test_can_get_all_clients()
    {
        $this->authenticateUser();
        Client::factory()->count(3)->create();

        $response = $this->getJson('/api/clients');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test creating a new client.
     */
    public function test_can_create_client()
    {
        $this->authenticateUser();
        $clientData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address
        ];

        $response = $this->postJson('/api/clients', $clientData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Client created successfully',
                     'data' => array_merge($clientData, [
                         'id' => 1,
                         'user_id' => 1,
                         'created_at' => $response->json('data.created_at'),
                         'updated_at' => $response->json('data.updated_at')
                     ])
                 ]);

        $this->assertDatabaseHas('clients', [
            'name' => $clientData['name'],
            'email' => $clientData['email'],
            'phone' => $clientData['phone'],
            'address' => $clientData['address']
        ]);
    }

    /**
     * Test validation when creating a client.
     */
    public function test_client_creation_requires_valid_data()
    {
        $this->authenticateUser();
        // Missing required fields
        $response = $this->postJson('/api/clients', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'phone']);

        // Invalid email
        $response = $this->postJson('/api/clients', [
            'name' => $this->faker->name,
            'email' => 'invalid-email',
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
        $this->authenticateUser();
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
        $this->authenticateUser();
        $client = Client::factory()->create();

        $updatedData = [
            'name' => 'Updated Client',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'address' => 'Updated Address',
        ];

        $response = $this->putJson('/api/clients/' . $client->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Client updated successfully'
                 ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Client',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'address' => 'Updated Address',
        ]);
    }

    /**
     * Test validation when updating a client.
     */
    public function test_client_update_requires_valid_data()
    {
        $this->authenticateUser();
        $client = Client::factory()->create();

        // Missing required fields
        $response = $this->putJson('/api/clients/' . $client->id, []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'phone']);

        // Invalid email
        $response = $this->putJson('/api/clients/' . $client->id, [
            'name' => $this->faker->name,
            'email' => 'invalid-email',
            'phone' => $this->faker->phoneNumber,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test deleting a client.
     */
    public function test_can_delete_client()
    {
        $this->authenticateUser();
        $client = Client::factory()->create();

        $response = $this->deleteJson('/api/clients/' . $client->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Client deleted successfully'
                 ]);

        $this->assertSoftDeleted('clients', [
            'id' => $client->id
        ]);
    }

    /**
     * Test error when client not found.
     */
    public function test_returns_404_when_client_not_found()
    {
        $this->authenticateUser();
        $response = $this->getJson('/api/clients/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/clients/999', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/clients/999');
        $response->assertStatus(404);
    }
}
