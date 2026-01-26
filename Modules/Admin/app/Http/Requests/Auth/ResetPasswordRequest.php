<?php

namespace Modules\Admin\app\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email|exists:admins,email',
            'password' => 'required|string|min:8|confirmed|different:old_password',
            'password_confirmation' => 'required',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'password.required' => __('admin.password_required'),
            'password.min' => __('admin.password_min_8'),
            'password.confirmed' => __('admin.password_confirmed'),
            'password.different' => __('admin.password_different'),
            'email.exists' => __('admin.email_not_found'),
        ];
    }
}
