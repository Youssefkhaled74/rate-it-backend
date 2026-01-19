<?php

namespace App\Modules\Admin\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name_en' => 'sometimes|required|string|max:255',
            'name_ar' => 'sometimes|nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'logo' => 'sometimes|nullable|string|max:1024',
        ];
    }
}
