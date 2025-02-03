<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\Response\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

final class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'uid' => 'string|required',
            'app_id' => 'string|required',
            'language' => 'string|required|size:2',
            'operating_system' => 'string|required|in:android,ios',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'uid.required' => 'UID is required.',
            'app_id.required' => 'App ID is required.',
            'language.required' => 'Language is required.',
            'language.size' => 'Language must be 2 characters.',
            'operating_system.required' => 'Operating System is required.',
            'operating_system.in' => 'Operating System must be android or ios.',
        ];
    }

    public function wantsJson(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        $response = ApiResponse::response(
            httpCode: 422,
            data: $validator->errors()->all(),
            success: false,
            message: 'Validation Error'
        );

        throw new ValidationException($validator, $response);
    }
}
