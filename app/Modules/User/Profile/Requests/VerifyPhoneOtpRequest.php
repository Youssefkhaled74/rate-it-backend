<?php

namespace App\Modules\User\Profile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPhoneOtpRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() != null;
    }

    public function rules()
    {
        return [
            'phone' => 'required|string',
            'otp' => 'required|string',
        ];
    }
}
