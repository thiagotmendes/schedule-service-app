<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Client;
use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles needed for testing
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'client']);
        Role::create(['name' => 'provider']);
    }

    public function test_user_can_have_client_profile()
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(Client::class, $user->client);
        $this->assertEquals($user->id, $client->user_id);
    }

    public function test_user_can_have_provider_profile()
    {
        $user = User::factory()->create();
        $user->assignRole('provider');
        
        $provider = Provider::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(Provider::class, $user->provider);
        $this->assertEquals($user->id, $provider->user_id);
    }

    public function test_user_can_have_multiple_roles()
    {
        $user = User::factory()->create();
        $user->assignRole(['admin', 'provider']);
        
        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('provider'));
        $this->assertFalse($user->hasRole('client'));
    }

    public function test_user_can_be_removed_from_role()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $this->assertTrue($user->hasRole('admin'));
        
        $user->removeRole('admin');
        $this->assertFalse($user->hasRole('admin'));
    }
} 