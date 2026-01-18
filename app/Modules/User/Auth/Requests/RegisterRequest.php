<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required','string','max:255'],
            'phone' => ['required','string','max:20','unique:users,phone'],
            'email' => ['required','email','max:255','unique:users,email'],
            'birth_date' => ['nullable','date'],
            'gender' => ['nullable','in:male,female,other'],
            'nationality' => ['nullable','string'],
            'password' => ['required','string','min:8','confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __("auth.email_taken"),
            'phone.unique' => __("auth.phone_taken"),
            'email.required' => __('validation.required'),
            'phone.required' => __('validation.required'),
            'password.confirmed' => __('validation.confirmed'),
        ];
    }
}
