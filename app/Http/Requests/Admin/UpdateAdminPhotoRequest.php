<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminPhotoRequest extends FormRequest
{
    public function authorize()
    {
        // only logged in admins can update their photo; controller will check for self/super
        return auth()->guard('admin_web')->check();
    }

    public function rules()
    {
        return [
            'photo' => ['required','file','image','mimes:jpg,jpeg,png,webp','max:3072'],
        ];
    }
}
