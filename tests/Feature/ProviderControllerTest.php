<?php

namespace Tests\Feature;

use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProviderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test retrieving all providers.
     */
    public function test_can_get_all_providers()
    {
        // Create some providers
        Provider::factory()->count(3)->create();

        // Make request to index endpoint
        $response = $this->getJson('/api/providers');

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test creating a new provider.
     */
    public function test_can_create_provider()
    {
        $providerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'document' => $this->faker->numerify('###########'), // 11 digits for document
        ];

        $response = $this->postJson('/api/providers', $providerData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'message' => 'Provider created successfully',
                     'data' => $providerData
                 ]);

        $this->assertDatabaseHas('providers', [
            'email' => $providerData['email'],
            'document' => $providerData['document']
        ]);
    }

    /**
     * Test validation when creating a provider.
     */
    public function test_provider_creation_requires_valid_data()
    {
        // Missing required fields
        $response = $this->postJson('/api/providers', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'phone', 'document']);

        // Invalid email
        $response = $this->postJson('/api/providers', [
            'name' => $this->faker->name,
            'email' => 'not-an-email',
            'phone' => $this->faker->phoneNumber,
            'document' => $this->faker->numerify('###########'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);

        // Invalid document length (must be exactly 11 characters)
        $response = $this->postJson('/api/providers', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'document' => '12345', // Too short
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['document']);
    }

    /**
     * Test retrieving a specific provider.
     */
    public function test_can_get_single_provider()
    {
        $provider = Provider::factory()->create();

        $response = $this->getJson('/api/providers/' . $provider->id);

        $response->assertStatus(200)
                 ->assertJson($provider->toArray());
    }

    /**
     * Test updating a provider.
     */
    public function test_can_update_provider()
    {
        $provider = Provider::factory()->create();

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '555-1234',
            'document' => '12345678901',
        ];

        $response = $this->putJson('/api/providers/' . $provider->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Provider updated successfully'
                 ]);

        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'document' => '12345678901',
        ]);
    }

    /**
     * Test validation when updating a provider.
     */
    public function test_provider_update_requires_valid_data()
    {
        $provider = Provider::factory()->create();

        // Missing required fields
        $response = $this->putJson('/api/providers/' . $provider->id, []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'phone', 'document']);

        // Invalid document length
        $response = $this->putJson('/api/providers/' . $provider->id, [
            'name' => 'Test Name',
            'email' => 'test@example.com',
            'phone' => '123-456-7890',
            'document' => '1234567890', // 10 digits instead of 11
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['document']);
    }

    /**
     * Test deleting a provider.
     */
    public function test_can_delete_provider()
    {
        $provider = Provider::factory()->create();

        $response = $this->deleteJson('/api/providers/' . $provider->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Provider deleted successfully'
                 ]);

        $this->assertDatabaseMissing('providers', [
            'id' => $provider->id
        ]);
    }

    /**
     * Test error when provider not found.
     */
    public function test_returns_404_when_provider_not_found()
    {
        $response = $this->getJson('/api/providers/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/providers/999', [
            'name' => 'Test Provider',
            'email' => 'test@example.com',
            'phone' => '123-456-7890',
            'document' => '12345678901'
        ]);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/providers/999');
        $response->assertStatus(404);
    }
}
