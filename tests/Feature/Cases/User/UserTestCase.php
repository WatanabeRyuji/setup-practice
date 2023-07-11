<?php

declare(strict_types=1);

namespace Tests\Feature\Cases\User;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTestCase extends TestCase
{
    protected readonly User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);
    }

    /**
     * @return self
     */
    protected function actingAsUser(): self
    {
        Sanctum::actingAs($this->user);

        return $this;
    }
}
