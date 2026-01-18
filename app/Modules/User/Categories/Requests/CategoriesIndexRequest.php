<?php

namespace App\Modules\User\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriesIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'min:2'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function getLimit(): int
    {
        return min($this->input('limit', 50), 100);
    }
}
