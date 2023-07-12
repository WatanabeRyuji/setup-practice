<?php

declare(strict_types=1);

namespace App\DataTransferObjects\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Data;

class LoginViewModelData extends Data
{
    public function __construct(
        public readonly Authenticatable $user,
        public readonly string $token,
        public readonly string $refreshToken,
    ) {
    }
}
