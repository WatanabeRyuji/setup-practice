<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Auth;

use App\DataTransferObjects\Auth\ResetPasswordData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class ResetPasswordRequest extends FormRequest
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
            'token' => __('validation.attributes.user_id'),
            'email' => __('validation.attributes.user_id'),
            'password' => __('validation.attributes.user_id'),
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
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'], // passwordの制限によって正規表現等を使う
        ];
    }

    /**
     * @return string
     */
    protected function dataClass(): string
    {
        return ResetPasswordData::class;
    }
}
