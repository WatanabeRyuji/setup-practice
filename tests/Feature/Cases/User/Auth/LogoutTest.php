<?php

namespace Tests\Feature\Cases\User\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Cases\User\UserTestCase;

class LogoutTest extends UserTestCase
{
    use RefreshDatabase;

    public function test_can_logout(): void
    {
        $this->actingAsUser()
            ->postJson(route('api.logout'))
            ->assertOk();

        $this->assertCount(0, $this->user->tokens->toArray());
    }
}
