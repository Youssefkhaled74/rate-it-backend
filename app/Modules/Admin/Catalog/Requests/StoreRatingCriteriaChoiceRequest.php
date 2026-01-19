<?php

namespace App\Modules\Admin\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingCriteriaChoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'value' => ['nullable','numeric'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer'],
        ];
    }
}
