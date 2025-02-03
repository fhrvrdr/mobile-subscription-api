<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\Response\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

final class PurchaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receipt' => 'string|required',
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
            'receipt.required' => 'Recepit is required.',
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
