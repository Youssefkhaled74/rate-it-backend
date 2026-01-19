<?php

namespace App\Modules\Admin\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
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
            'address_en' => ['nullable','string','max:1024'],
            'address_ar' => ['nullable','string','max:1024'],
            'phone' => ['nullable','string','max:50'],
            'lat' => ['nullable','numeric'],
            'lng' => ['nullable','numeric'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}
