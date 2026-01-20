<?php

namespace App\Modules\Admin\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HideReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_hidden' => ['required','boolean'],
            'reason' => ['nullable','string','max:1000'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_hidden')) {
            $this->merge(['is_hidden' => filter_var($this->input('is_hidden'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)]);
        }
    }

    public function withValidator($validator)
    {
        $validator->sometimes('reason', ['required','string','max:1000'], function ($input) {
            return isset($input->is_hidden) && $input->is_hidden;
        });
    }
}
