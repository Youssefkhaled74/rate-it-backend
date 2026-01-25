<?php

namespace App\Modules\Admin\Vendors\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListVendorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => 'nullable|integer|exists:brands,id',
            'search' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
