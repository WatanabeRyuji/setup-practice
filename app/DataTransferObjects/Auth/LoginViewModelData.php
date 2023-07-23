<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Auth;

use App\Models\User;
use Spatie\LaravelData\Data;

class LoginViewModelData extends Data
{
    public function __construct(
        public readonly User $user,
        public readonly string $token,
        public readonly string $refreshToken,
    ) {
    }
}
