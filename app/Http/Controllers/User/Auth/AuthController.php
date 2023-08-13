<?php

declare(strict_types=1);

namespace App\Http\Controllers\User\Auth;

use App\DataTransferObjects\Auth\LoginData;
use App\DataTransferObjects\Auth\LoginViewModelData;
use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\ForgotPasswordRequest;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\ResetPasswordRequest;
use App\Http\Requests\User\Auth\SignupRequest;
use App\Models\User;
use App\ViewModel\User\Auth\ForgotPasswordViewModel;
use App\ViewModel\User\Auth\LoginViewModel;
use App\ViewModel\User\Auth\ResetPasswordViewModel;
use Carbon\CarbonImmutable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Exceptions\InvalidDataClass;

class AuthController extends Controller
{
    use ThrottlesLogins;

    protected int $maxAttempts = 5;

    protected int $decayMinutes = 1;

    /**
     * @param LoginRequest $request
     * @throws ValidationException
     * @throws InvalidDataClass|\Throwable
     * @throws AuthenticationException
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
        //TODO: email_verified_atはどうするか考える

        if (! Auth::attempt($request->toArray())) {
            $this->incrementLoginAttempts($request);
            throw new AuthenticationException('メールアドレスまたはパスワードが違います'); // 本来はlangファイルに定義する;
        }

        [$accessToken, $refreshToken] = DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            return [
                $user->createToken('access_token', [TokenAbility::AccessApi], CarbonImmutable::now()->addMinutes(config('sanctum.expiration')))->plainTextToken,
                $user->createToken('refresh_token', [TokenAbility::RefreshToken], CarbonImmutable::now()->addMinutes(config('rt_expiration')))->plainTextToken,
            ];
        });

        $this->clearLoginAttempts($request);

        return response()->json(new LoginViewModel(new LoginViewModelData($user, $accessToken, $refreshToken)));
    }

    /**
     * @throws \Throwable
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        //TODO: email_verified_atはどうするか考える
        [$accessToken, $refreshToken] = DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            return [
                $user->createToken('access_token', [TokenAbility::AccessApi], CarbonImmutable::now()->addMinutes(config('sanctum.expiration')))->plainTextToken,
                $user->createToken('refresh_token', [TokenAbility::RefreshToken], CarbonImmutable::now()->addMinutes(config('rt_expiration')))->plainTextToken,
            ];
        });

        return response()->json(new LoginViewModel(new LoginViewModelData($user, $accessToken, $refreshToken)));
    }

    /**
     * @inheritDoc
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }

    /**
     * @param SignupRequest $request
     * @throws InvalidDataClass
     * @throws \Throwable
     * @return JsonResponse
     */
    public function register(SignupRequest $request): JsonResponse
    {
        $data = $request->getData();
        [$user, $accessToken, $refreshToken] = DB::transaction(function () use ($data) {
            $user = User::create($data->toArray());
            Auth::attempt($data->toArray());
            //TODO: email_verified_atはどうするか考える
            //            event(new Registered($user));
            return [
                $user,
                $user->createToken('access_token', [TokenAbility::AccessApi], CarbonImmutable::now()->addMinutes(config('sanctum.expiration')))->plainTextToken,
                $user->createToken('refresh_token', [TokenAbility::RefreshToken], CarbonImmutable::now()->addMinutes(config('rt_expiration')))->plainTextToken,
            ];
        });

        return response()->json(new LoginViewModel(new LoginViewModelData($user, $accessToken, $refreshToken)));
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()?->tokens()->delete();

        return response()->json();
    }

    /**
     * @param ForgotPasswordRequest $request
     * @throws InvalidDataClass
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        // カスタムなどをしたい場合は参照: https://readouble.com/laravel/10.x/ja/passwords.html
        $status = Password::sendResetLink($request->getData()->toArray());

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(new ForgotPasswordViewModel(__($status)));
        }

        return response()->json(['message' => __($status)], 500); // 仮
    }

    /**
     * @param ResetPasswordRequest $request
     * @throws InvalidDataClass
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->getData()->toArray(),
            static function (User $user, string $password) {
                $user->update(['password' => \Hash::make($password)]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(new ResetPasswordViewModel(__($status)));
        }

        return response()->json(['message' => __($status)], 500); // 仮
    }
}
