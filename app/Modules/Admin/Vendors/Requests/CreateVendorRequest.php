<?php

namespace App\Modules\Admin\Vendors\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => 'required|integer|exists:brands,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9+\-\s()]+$/|unique:vendor_users|max:20',
            'email' => 'nullable|email|unique:vendor_users|max:255',
            'password' => 'required|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'brand_id.required' => __('admin.vendors.brand_id_required'),
            'brand_id.exists' => __('admin.vendors.brand_id_invalid'),
            'name.required' => __('admin.vendors.name_required'),
            'phone.required' => __('admin.vendors.phone_required'),
            'phone.regex' => __('admin.vendors.phone_invalid'),
            'phone.unique' => __('admin.vendors.phone_already_exists'),
            'email.email' => __('admin.vendors.email_invalid'),
            'email.unique' => __('admin.vendors.email_already_exists'),
            'password.required' => __('admin.vendors.password_required'),
            'password.min' => __('admin.vendors.password_min'),
            'password.confirmed' => __('admin.vendors.password_confirmation_failed'),
            'photo.image' => __('admin.vendors.photo_invalid'),
            'photo.mimes' => __('admin.vendors.photo_invalid_format'),
            'photo.max' => __('admin.vendors.photo_too_large'),
        ];
    }
}
