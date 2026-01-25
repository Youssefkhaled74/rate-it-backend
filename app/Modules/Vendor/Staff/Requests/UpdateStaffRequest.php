<?php

namespace App\Modules\Vendor\Staff\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $staffId = $this->route('id');
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
