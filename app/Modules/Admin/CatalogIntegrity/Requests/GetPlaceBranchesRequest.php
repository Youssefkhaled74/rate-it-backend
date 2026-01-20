<?php

namespace App\Modules\Admin\CatalogIntegrity\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPlaceBranchesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
        ];
    }
}
