<?php

namespace App\Modules\Admin\LoyaltySettings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoyaltySettingsIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:200'],
        ];
    }
}
