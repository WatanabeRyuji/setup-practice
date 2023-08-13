<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Auth;

use App\DataTransferObjects\Auth\ForgotPasswordData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class ForgotPasswordRequest extends FormRequest
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
            'email' => __('validation.attributes.user_id'),
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
            'email' => ['required', 'email'],
        ];
    }

    /**
     * @return string
     */
    protected function dataClass(): string
    {
        return ForgotPasswordData::class;
    }
}
