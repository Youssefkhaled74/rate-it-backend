<?php

namespace App\Modules\Admin\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUsersIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'q' => ['nullable','string'],
            'phone' => ['nullable','string'],
            'email' => ['nullable','email'],
            'is_phone_verified' => ['nullable','boolean'],
            'is_blocked' => ['nullable','boolean'],
            'gender_id' => ['nullable','integer'],
            'nationality_id' => ['nullable','integer'],
            'created_from' => ['nullable','date'],
            'created_to' => ['nullable','date'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:200'],
        ];
    }
}
