<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->guard('admin_web')->check();
    }

    public function rules()
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:admins,email'],
            'phone' => ['nullable','string','max:50','unique:admins,phone'],
            'password' => ['required','string','min:8'],
            'role' => ['nullable','string','max:50'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}
