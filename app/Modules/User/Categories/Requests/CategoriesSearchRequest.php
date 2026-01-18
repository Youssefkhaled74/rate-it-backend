<?php

namespace App\Modules\User\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriesSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:2'],
            'types' => ['nullable', 'string'], // comma separated
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function getLimit(): int
    {
        return min($this->input('limit', 20), 50);
    }

    public function getTypes(): array
    {
        $raw = $this->input('types', 'categories,subcategories');
        return array_filter(array_map('trim', explode(',', $raw)));
    }
}
