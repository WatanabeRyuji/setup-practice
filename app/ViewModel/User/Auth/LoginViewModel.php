<?php

declare(strict_types=1);

namespace App\ViewModel\User\Auth;

use App\DataTransferObjects\Auth\LoginViewModelData;
use App\ViewModel\ViewModel;

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
            'refresh_token' => $this->loginViewModelData->refreshToken,
        ];
    }
}
