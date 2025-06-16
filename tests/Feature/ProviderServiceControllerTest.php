<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProviderServiceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test attaching services to a provider.
     */
    public function test_can_attach_services_to_provider()
    {
        // Create a provider and services
        $provider = Provider::factory()->create();
        $services = Service::factory()->count(3)->create();
        $serviceIds = $services->pluck('id')->toArray();

        // Make request to attach services
        $response = $this->postJson("/api/providers/{$provider->id}/services", [
            'service_ids' => $serviceIds
        ]);

        // Assert response status and message
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Serviços vinculados com sucesso'
                 ]);

        // Check that the services are attached to the provider
        foreach ($serviceIds as $serviceId) {
            $this->assertDatabaseHas('provider_service', [
                'provider_id' => $provider->id,
                'service_id' => $serviceId
            ]);
        }
    }

    /**
     * Test validation when attaching services.
     */
    public function test_attaching_services_requires_valid_data()
    {
        $provider = Provider::factory()->create();

        // Missing service_ids
        $response = $this->postJson("/api/providers/{$provider->id}/services", []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['service_ids']);

        // service_ids not an array
        $response = $this->postJson("/api/providers/{$provider->id}/services", [
            'service_ids' => 'not-an-array'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['service_ids']);

        // Invalid service ID
        $response = $this->postJson("/api/providers/{$provider->id}/services", [
            'service_ids' => [999] // Non-existent service ID
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['service_ids.0']);
    }

    /**
     * Test error when provider not found.
     */
    public function test_returns_404_when_provider_not_found()
    {
        $services = Service::factory()->count(2)->create();
        $serviceIds = $services->pluck('id')->toArray();

        $response = $this->postJson("/api/providers/999/services", [
            'service_ids' => $serviceIds
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test that services are not detached when attaching new ones.
     */
    public function test_existing_services_are_not_detached()
    {
        // Create a provider and services
        $provider = Provider::factory()->create();
        $existingServices = Service::factory()->count(2)->create();
        $newServices = Service::factory()->count(2)->create();

        // Attach existing services
        $provider->services()->attach($existingServices->pluck('id'));

        // Make request to attach new services
        $response = $this->postJson("/api/providers/{$provider->id}/services", [
            'service_ids' => $newServices->pluck('id')->toArray()
        ]);

        $response->assertStatus(200);

        // Check that both existing and new services are attached
        foreach ($existingServices->pluck('id') as $serviceId) {
            $this->assertDatabaseHas('provider_service', [
                'provider_id' => $provider->id,
                'service_id' => $serviceId
            ]);
        }

        foreach ($newServices->pluck('id') as $serviceId) {
            $this->assertDatabaseHas('provider_service', [
                'provider_id' => $provider->id,
                'service_id' => $serviceId
            ]);
        }

        // Provider should have 4 services in total
        $this->assertEquals(4, $provider->fresh()->services()->count());
    }
}
