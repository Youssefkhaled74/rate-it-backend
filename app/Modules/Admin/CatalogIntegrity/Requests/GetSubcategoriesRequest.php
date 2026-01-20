<?php

namespace App\Modules\Admin\CatalogIntegrity\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetSubcategoriesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'q' => ['nullable','string','max:255'],
        ];
    }
}
