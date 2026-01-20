<?php

namespace App\Modules\Admin\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_blocked' => ['required','boolean'],
            'reason' => ['nullable','string','max:1000'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_blocked')) {
            $this->merge(['is_blocked' => filter_var($this->input('is_blocked'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)]);
        }
    }

    public function withValidator($validator)
    {
        $validator->sometimes('reason', ['required','string','max:1000'], function ($input) {
            return isset($input->is_blocked) && $input->is_blocked;
        });
    }
}
