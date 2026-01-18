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
            'phone' => ['required','string','unique:users,phone'],
            'email' => ['nullable','email'],
            'birth_date' => ['nullable','date'],
            'gender' => ['nullable','in:male,female'],
            'nationality' => ['nullable','string'],
            'password' => ['required','string','min:8','confirmed'],
        ];
    }
}
