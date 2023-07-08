<?php

declare(strict_types=1);

namespace App\Http\Controllers\User\Auth;

use App\DataTransferObjects\User\LoginData;
use App\DataTransferObjects\User\LoginViewModelData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Models\User;
use App\ViewModel\User\LoginViewModel;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Exceptions\InvalidDataClass;

class LoginController extends Controller
{
    use ThrottlesLogins;

    protected int $maxAttempts = 5;

    protected int $decayMinutes = 1;

    /**
     * @param LoginRequest $request
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws InvalidDataClass
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        /** @var LoginData $requestData */
        $requestData = $request->getData();
        /** @var User $user */
        $user = User::whereEmail($requestData->email)->first();

        if (is_null($user) || ! Hash::check($requestData->password, $user->password)) {
            $this->incrementLoginAttempts($request);
            throw new AuthenticationException('メールアドレスまたはパスワードが違います'); // 本来はlangファイルに定義する;
        }

        $this->clearLoginAttempts($request);
        $user->createToken(config('app.name') . '_token')->plainTextToken;

        return response()->json(
            new LoginViewModel(
                new LoginViewModelData($user, $user->createToken(config('app.name') . '_token')->plainTextToken)
            )
        );
    }

    /**
     * @inheritDoc
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
}
