<?php

namespace App\Modules\Admin\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRatingCriteriaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name_en' => ['sometimes','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'type' => ['nullable','string','in:RATING,YES_NO,MULTIPLE_CHOICE'],
            'subcategory_id' => ['nullable','integer','exists:subcategories,id'],
            'is_required' => ['nullable','boolean'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0'],
        ];
    }
}
