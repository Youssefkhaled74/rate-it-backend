<?php

namespace App\Modules\Admin\LoyaltySettings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoyaltySettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'points_per_review' => ['required','integer','min:0'],
            'point_value_money' => ['required','numeric','gt:0'],
            'currency' => ['required','string','size:3'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('currency')) {
            $this->merge(['currency' => strtoupper($this->input('currency'))]);
        }
    }
}
