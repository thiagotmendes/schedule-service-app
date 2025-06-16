<?php

namespace Tests\Unit;

use App\Models\Provider;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_belongs_to_user()
    {
        $user = User::factory()->create();
        $provider = Provider::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $provider->user);
        $this->assertEquals($user->id, $provider->user->id);
    }

    public function test_provider_can_have_services()
    {
        $provider = Provider::factory()->create();
        $service = Service::factory()->create();
        
        $provider->services()->attach($service->id);
        
        $this->assertTrue($provider->services->contains($service));
    }

    public function test_provider_can_have_appointments()
    {
        $provider = Provider::factory()->create();
        $appointment = Appointment::factory()->create(['provider_id' => $provider->id]);

        $this->assertInstanceOf(Appointment::class, $provider->appointments->first());
        $this->assertEquals($provider->id, $appointment->provider_id);
    }

    public function test_provider_has_correct_attributes()
    {
        $provider = Provider::factory()->create([
            'specialization' => 'Test Specialization',
            'bio' => 'Test Bio'
        ]);

        $this->assertEquals('Test Specialization', $provider->specialization);
        $this->assertEquals('Test Bio', $provider->bio);
    }

    public function test_provider_can_be_updated()
    {
        $provider = Provider::factory()->create([
            'specialization' => 'Original Specialization',
            'bio' => 'Original Bio'
        ]);

        $provider->update([
            'specialization' => 'Updated Specialization',
            'bio' => 'Updated Bio'
        ]);

        $this->assertEquals('Updated Specialization', $provider->fresh()->specialization);
        $this->assertEquals('Updated Bio', $provider->fresh()->bio);
    }
} 