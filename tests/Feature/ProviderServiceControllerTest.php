<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\AuthenticatesUser;

class ProviderServiceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, AuthenticatesUser;

    /**
     * Test attaching services to a provider.
     */
    public function test_can_attach_services_to_provider()
    {
        $this->authenticateUser();
        $provider = Provider::factory()->create();
        $services = Service::factory()->count(3)->create();
        $serviceIds = $services->pluck('id')->toArray();

        $response = $this->postJson("/api/providers/{$provider->id}/services", [
            'service_ids' => $serviceIds
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Services attached successfully'
                 ]);

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
        $this->authenticateUser();
        $provider = Provider::factory()->create();

        // Missing service_ids
        $response = $this->postJson("/api/providers/{$provider->id}/services", []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['service_ids']);

        // Empty service_ids array
        $response = $this->postJson("/api/providers/{$provider->id}/services", [
            'service_ids' => []
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['service_ids']);
    }

    /**
     * Test error when provider not found.
     */
    public function test_returns_404_when_provider_not_found()
    {
        $this->authenticateUser();
        $services = Service::factory()->count(3)->create();
        $serviceIds = $services->pluck('id')->toArray();

        $response = $this->postJson("/api/providers/999/services", [
            'service_ids' => $serviceIds
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test that existing services are not detached when attaching new ones.
     */
    public function test_existing_services_are_not_detached()
    {
        $this->authenticateUser();
        $provider = Provider::factory()->create();
        $existingServices = Service::factory()->count(2)->create();
        $provider->services()->attach($existingServices->pluck('id'));

        $newServices = Service::factory()->count(2)->create();
        $newServiceIds = $newServices->pluck('id')->toArray();

        $response = $this->postJson("/api/providers/{$provider->id}/services", [
            'service_ids' => $newServiceIds
        ]);

        $response->assertStatus(200);

        // Check that both existing and new services are attached
        foreach ($existingServices as $service) {
            $this->assertDatabaseHas('provider_service', [
                'provider_id' => $provider->id,
                'service_id' => $service->id
            ]);
        }

        foreach ($newServiceIds as $serviceId) {
            $this->assertDatabaseHas('provider_service', [
                'provider_id' => $provider->id,
                'service_id' => $serviceId
            ]);
        }
    }
}
