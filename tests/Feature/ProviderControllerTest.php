<?php

namespace Tests\Feature;

use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\AuthenticatesUser;

class ProviderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, AuthenticatesUser;

    /**
     * Test retrieving all providers.
     */
    public function test_can_get_all_providers()
    {
        $this->authenticateUser();
        Provider::factory()->count(3)->create();

        $response = $this->getJson('/api/providers');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test creating a new provider.
     */
    public function test_can_create_provider()
    {
        $this->authenticateUser();
        $providerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'document' => $this->faker->numerify('###########'),
            'specialization' => $this->faker->word,
            'bio' => $this->faker->paragraph
        ];

        $response = $this->postJson('/api/providers', $providerData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Provider created successfully',
                     'data' => array_merge($providerData, [
                         'id' => 1,
                         'user_id' => 1,
                         'created_at' => $response->json('data.created_at'),
                         'updated_at' => $response->json('data.updated_at')
                     ])
                 ]);

        $this->assertDatabaseHas('providers', [
            'name' => $providerData['name'],
            'email' => $providerData['email'],
            'phone' => $providerData['phone'],
            'document' => $providerData['document'],
            'specialization' => $providerData['specialization'],
            'bio' => $providerData['bio']
        ]);
    }

    /**
     * Test validation when creating a provider.
     */
    public function test_provider_creation_requires_valid_data()
    {
        $this->authenticateUser();
        // Missing required fields
        $response = $this->postJson('/api/providers', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'phone', 'document']);

        // Invalid email
        $response = $this->postJson('/api/providers', [
            'name' => $this->faker->name,
            'email' => 'invalid-email',
            'phone' => $this->faker->phoneNumber,
            'document' => $this->faker->numerify('###########'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test retrieving a specific provider.
     */
    public function test_can_get_single_provider()
    {
        $this->authenticateUser();
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
        $this->authenticateUser();
        $provider = Provider::factory()->create();

        $updatedData = [
            'name' => 'Updated Provider',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'document' => '12345678901',
            'specialization' => 'Updated Specialization',
            'bio' => 'Updated Bio',
        ];

        $response = $this->putJson('/api/providers/' . $provider->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Provider updated successfully'
                 ]);

        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'name' => 'Updated Provider',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'document' => '12345678901',
            'specialization' => 'Updated Specialization',
            'bio' => 'Updated Bio',
        ]);
    }

    /**
     * Test validation when updating a provider.
     */
    public function test_provider_update_requires_valid_data()
    {
        $this->authenticateUser();
        $provider = Provider::factory()->create();

        // Missing required fields
        $response = $this->putJson('/api/providers/' . $provider->id, []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'phone', 'document']);

        // Invalid email
        $response = $this->putJson('/api/providers/' . $provider->id, [
            'name' => $this->faker->name,
            'email' => 'invalid-email',
            'phone' => $this->faker->phoneNumber,
            'document' => $this->faker->numerify('###########'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test deleting a provider.
     */
    public function test_can_delete_provider()
    {
        $this->authenticateUser();
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
        $this->authenticateUser();
        $response = $this->getJson('/api/providers/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/providers/999', [
            'name' => 'Test Provider',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'document' => '12345678901',
        ]);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/providers/999');
        $response->assertStatus(404);
    }
}
