<?php

namespace App\Modules\Admin\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderSubcategoryCriteriaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'criteria_ids' => ['required', 'array', 'min:1'],
            'criteria_ids.*' => ['required', 'integer', 'distinct'],
        ];
    }
}
