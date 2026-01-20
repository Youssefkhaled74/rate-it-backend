<?php

namespace App\Modules\Admin\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncSubcategoryCriteriaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.criteria_id' => ['required', 'integer', 'distinct'],
            'items.*.is_required' => ['nullable', 'boolean'],
            'items.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}
