<?php

declare(strict_types=1);

namespace App\ViewModel\User;

use App\DataTransferObjects\User\LoginViewModelData;
use App\ViewModel\ViewModel;

/**
 * @extends ViewModel
 */
class LoginViewModel extends ViewModel
{
    public function __construct(private readonly LoginViewModelData $loginViewModelData)
    {
    }

    /**
     * @return array{email: string, name: string, token: string}
     */
    public function toMap(): array
    {
        return [
            'name' => $this->loginViewModelData->user->name,
            'email' => $this->loginViewModelData->user->email,
            'token' => $this->loginViewModelData->token,
        ];
    }
}
