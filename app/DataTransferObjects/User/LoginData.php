<?php

declare(strict_types=1);

namespace App\DataTransferObjects\User;

use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {
    }
}
