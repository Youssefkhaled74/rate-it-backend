<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->guard('admin_web')->check();
    }

    public function rules()
    {
        $adminId = $this->route('admin')?->id ?? null;
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:admins,email,' . $adminId],
            'phone' => ['nullable','string','max:50','unique:admins,phone,' . $adminId],
            'password' => ['nullable','string','min:8'],
            'role' => ['nullable','string','max:50'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}
