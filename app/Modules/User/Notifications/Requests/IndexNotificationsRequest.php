<?php

namespace App\Modules\User\Notifications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexNotificationsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'unread_only' => ['nullable', 'in:0,1,true,false'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('unread_only')) {
            $val = $this->input('unread_only');
            if (is_string($val) && in_array(strtolower($val), ['1','true'], true)) {
                $this->merge(['unread_only' => true]);
            } elseif (is_string($val) && in_array(strtolower($val), ['0','false'], true)) {
                $this->merge(['unread_only' => false]);
            }
        }
    }
}
