<?php

namespace App\Modules\User\Home\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomeSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
            'types' => ['nullable', 'string'],
        ];
    }
}
