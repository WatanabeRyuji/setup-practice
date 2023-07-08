<?php

declare(strict_types=1);

namespace Tests\Feature\Cases\User\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Cases\User\UserTestCase;

class LoginTest extends UserTestCase
{
    use RefreshDatabase;

    private readonly User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * 正しいログイン情報でログインできる
     * @return void
     */
    public function test_can_login_with_valid_data(): void
    {
        $this->postJson(route('api.login'), [
            'email' => 'test@test.com',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('name', $this->user->name)
                    ->where('email', $this->user->email)
                    ->has('token')
            );
    }

    /**
     * アドレスが違うとログインできない
     * @return void
     */
    public function test_can_not_login_with_wrong_email(): void
    {
        $this->postJson(route('api.login'), [
            'email' => 'wrong@wrong.com',
            'password' => 'password',
        ])
            ->assertUnauthorized()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('message')
            );

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
        ]);
    }

    /**
     * アドレスが違うとログインできない
     * @return void
     */
    public function test_can_not_login_with_wrong_password(): void
    {
        $this->postJson(route('api.login'), [
            'email' => 'test@test.com',
            'password' => 'wrong',
        ])
            ->assertUnauthorized()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('message')
            );

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
        ]);
    }
}
