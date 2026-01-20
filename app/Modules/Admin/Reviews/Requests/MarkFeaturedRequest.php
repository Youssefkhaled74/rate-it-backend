<?php

namespace App\Modules\Admin\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkFeaturedRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_featured' => ['required','boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_featured')) {
            $this->merge(['is_featured' => filter_var($this->input('is_featured'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)]);
        }
    }
}
