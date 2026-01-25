<?php

namespace App\Modules\Vendor\Staff\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^[0-9+\-\s()]+$/', 'max:20', 'unique:vendor_users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:vendor_users,email'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ];
    }

    public function messages()
    {
        return [
            'phone.regex' => trans('auth.invalid_phone'),
            'phone.unique' => trans('auth.phone_already_exists'),
            'email.unique' => trans('auth.email_already_exists'),
            'branch_id.exists' => trans('validation.exists'),
        ];
    }
}
