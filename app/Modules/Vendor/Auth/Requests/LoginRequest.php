<?php

namespace App\Modules\Vendor\Auth\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class LoginRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('validation.required', ['attribute' => 'phone']),
            'phone.regex' => __('validation.phone'),
            'password.required' => __('validation.required', ['attribute' => 'password']),
            'password.min' => __('validation.min.string', ['attribute' => 'password', 'min' => 6]),
        ];
    }
}
