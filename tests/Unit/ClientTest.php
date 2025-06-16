<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_belongs_to_user()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $client->user);
        $this->assertEquals($user->id, $client->user->id);
    }

    public function test_client_can_have_appointments()
    {
        $client = Client::factory()->create();
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->assertInstanceOf(Appointment::class, $client->appointments->first());
        $this->assertEquals($client->id, $appointment->client_id);
    }

    public function test_client_has_correct_attributes()
    {
        $client = Client::factory()->create([
            'phone' => '1234567890',
            'address' => 'Test Address'
        ]);

        $this->assertEquals('1234567890', $client->phone);
        $this->assertEquals('Test Address', $client->address);
    }

    public function test_client_can_be_updated()
    {
        $client = Client::factory()->create([
            'phone' => '1234567890',
            'address' => 'Original Address'
        ]);

        $client->update([
            'phone' => '9876543210',
            'address' => 'Updated Address'
        ]);

        $this->assertEquals('9876543210', $client->fresh()->phone);
        $this->assertEquals('Updated Address', $client->fresh()->address);
    }

    public function test_client_can_have_multiple_appointments()
    {
        $client = Client::factory()->create();
        $appointment1 = Appointment::factory()->create(['client_id' => $client->id]);
        $appointment2 = Appointment::factory()->create(['client_id' => $client->id]);

        $this->assertCount(2, $client->appointments);
        $this->assertTrue($client->appointments->contains($appointment1));
        $this->assertTrue($client->appointments->contains($appointment2));
    }
} 