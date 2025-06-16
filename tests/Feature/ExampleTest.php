<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // The root route redirects to /api/documentation, so we expect a 302 response
        $response->assertStatus(302)
                 ->assertRedirect('/api/documentation');

        // Follow the redirect and check that the documentation page loads successfully
        $this->get('/api/documentation')->assertStatus(200);
    }
}
