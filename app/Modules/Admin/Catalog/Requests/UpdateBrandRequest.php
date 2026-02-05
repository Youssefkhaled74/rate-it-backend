<?php

namespace App\Modules\Admin\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
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
            'logo' => ['nullable','string','max:1024'],
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer'],
        ];
    }
}
