<?php

namespace Tests\Feature\Cases\User\Auth;

use App\Enums\TokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Cases\User\UserTestCase;

class RefreshTest extends UserTestCase
{
    use RefreshDatabase;

    public function test_can_refresh_acccess_token(): void
    {
        Sanctum::actingAs($this->user, [TokenAbility::RefreshToken]);

        $this->postJson(route('api.refresh_token'))
            ->assertOk()
            ->assertJson(
                fn ($json) => $json
                    ->where('name', $this->user->name)
                    ->where('email', $this->user->email)
                    ->has('token')
                    ->has('refresh_token')
            );
    }

    public function test_can_not_refresh_access_token_with_send_access_token(): void
    {
        $this->actingAsUser()
            ->postJson(route('api.refresh_token'))
            ->assertForbidden();
    }
}
