<?php

namespace App\Modules\Admin\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubcategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_id' => ['sometimes','integer','exists:categories,id'],
            'name_en' => ['sometimes','string','max:255'],
            'name_ar' => ['sometimes','string','max:255'],
            'image' => ['sometimes','nullable','string','max:1024'],
            'is_active' => ['sometimes','boolean'],
        ];
    }
}
