<?php

namespace App\Modules\Vendor\Staff\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetStaffPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'new_password.min' => trans('auth.password_min'),
        ];
    }
}
