<?php

namespace App\Modules\User\Subscriptions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plan_id' => ['nullable'],
            'plan_code' => ['nullable','string'],
            'provider' => ['nullable','string'],
        ];
    }

    protected function prepareForValidation()
    {
        // nothing for now
    }
}
