<?php

declare(strict_types=1);

namespace App\ViewModel\User\Auth;

use App\ViewModel\ViewModel;

class ResetPasswordViewModel extends ViewModel
{
    public function __construct(private readonly string $message)
    {
    }

    /**
     * @return array{message: string}
     */
    public function toMap(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
