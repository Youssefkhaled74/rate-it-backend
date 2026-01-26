<?php

namespace Modules\Admin\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->user()?->can('create', \Modules\Admin\app\Models\Admin::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'is_super' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('admin.name_required'),
            'email.required' => __('admin.email_required'),
            'email.email' => __('admin.email_invalid'),
            'email.unique' => __('admin.email_unique'),
            'password.required' => __('admin.password_required'),
            'password.min' => __('admin.password_min_8'),
            'password.confirmed' => __('admin.password_confirmed'),
            'status.required' => __('admin.status_required'),
        ];
    }
}
