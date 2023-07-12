<?php

declare(strict_types=1);

namespace App\Http\Controllers\User\Auth;

use App\DataTransferObjects\User\LoginData;
use App\DataTransferObjects\User\LoginViewModelData;
use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Models\User;
use App\ViewModel\User\LoginViewModel;
use Carbon\CarbonImmutable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        /** @var ?User $user */
        $user = User::whereEmail($requestData->email)->first();

        if (! Auth::attempt($request->toArray())) {
            $this->incrementLoginAttempts($request);
            throw new AuthenticationException('メールアドレスまたはパスワードが違います'); // 本来はlangファイルに定義する;
        }

        $accessToken = $user->createToken('access_token', [TokenAbility::AccessApi], CarbonImmutable::now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::RefreshToken], CarbonImmutable::now()->addMinutes(config('rt_expiration')))->plainTextToken;

        $user->tokens()->delete();
        $this->clearLoginAttempts($request);

        return response()->json(new LoginViewModel(new LoginViewModelData($user, $accessToken, $refreshToken)));
    }

    /**
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        /** @var Authenticatable $user */
        $user = auth()->user();
        $user->tokens()->delete();

        $accessToken = $user->createToken('access_token', [TokenAbility::AccessApi], CarbonImmutable::now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::RefreshToken], CarbonImmutable::now()->addMinutes(config('rt_expiration')))->plainTextToken;

        return response()->json(new LoginViewModel(new LoginViewModelData($user, $accessToken, $refreshToken)));
    }

    /**
     * @inheritDoc
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
}
