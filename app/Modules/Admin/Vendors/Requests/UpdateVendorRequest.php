<?php

namespace App\Modules\Admin\Vendors\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('vendor_users')->ignore($this->route('vendor')),
            ],
            'password' => 'sometimes|string|min:6|confirmed',
            'password_confirmation' => 'required_with:password',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('admin.vendors.name_required'),
            'email.email' => __('admin.vendors.email_invalid'),
            'email.unique' => __('admin.vendors.email_already_exists'),
            'password.min' => __('admin.vendors.password_min'),
            'password.confirmed' => __('admin.vendors.password_confirmation_failed'),
            'photo.image' => __('admin.vendors.photo_invalid'),
            'photo.mimes' => __('admin.vendors.photo_invalid_format'),
            'photo.max' => __('admin.vendors.photo_too_large'),
        ];
    }
}
