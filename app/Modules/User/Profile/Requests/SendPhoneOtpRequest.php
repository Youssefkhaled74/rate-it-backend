<?php

namespace App\Modules\User\Profile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendPhoneOtpRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() != null;
    }

    public function rules()
    {
        return [
            'phone' => 'required|string',
        ];
    }
}
