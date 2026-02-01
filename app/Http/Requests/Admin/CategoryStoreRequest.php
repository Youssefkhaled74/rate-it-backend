<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // لو عندك Policies خليها هنا
    }

    public function rules(): array
    {
        return [
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'logo' => ['nullable','image','mimes:png,jpg,jpeg,webp','max:4096'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ];
    }
}
