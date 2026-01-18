<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required','string'],
            'reset_token' => ['required','string'],
            'new_password' => ['required','string','min:8','confirmed'],
        ];
    }
}
