<?php

namespace App\Modules\User\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanQrRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'qr_code_value' => 'required|string|max:255',
        ];
    }
}
