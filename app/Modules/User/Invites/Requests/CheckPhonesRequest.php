<?php

namespace App\Modules\User\Invites\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckPhonesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phones' => 'required|array|min:1',
            'phones.*' => 'required|string',
        ];
    }
}
