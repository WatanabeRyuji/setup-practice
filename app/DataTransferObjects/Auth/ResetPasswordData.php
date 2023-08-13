<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Auth;

use Spatie\LaravelData\Data;

class ResetPasswordData extends Data
{
    public function __construct(
        public readonly string $token,
        public readonly string $email,
        public readonly string $password
    ) {
    }
}
