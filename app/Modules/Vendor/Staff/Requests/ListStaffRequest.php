<?php

namespace App\Modules\Vendor\Staff\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListStaffRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'q' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ];
    }
}
