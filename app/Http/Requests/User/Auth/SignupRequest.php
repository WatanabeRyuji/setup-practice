<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Auth;

use App\DataTransferObjects\Auth\SignupData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SignupRequest extends FormRequest
{
    use WithData;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string,string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('user.name'),
            'email' => __('user.email'),
            'password' => __('user.password'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'], // passwordの制限によって正規表現を使う
        ];
    }

    /**
     * @return string
     */
    protected function dataClass(): string
    {
        return SignupData::class;
    }
}
