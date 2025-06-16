<?php

namespace Tests\Traits;

use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait AuthenticatesUser
{
    /**
     * Authenticate a user for testing
     *
     * @param User|null $user
     * @return User
     */
    protected function authenticateUser(?User $user = null): User
    {
        $user = $user ?? User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }
} 