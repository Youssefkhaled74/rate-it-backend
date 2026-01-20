<?php

namespace App\Modules\Admin\Points\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPointsTransactionsIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => ['nullable','integer'],
            'type' => ['nullable','string'],
            'source' => ['nullable','string'],
            'date_from' => ['nullable','date'],
            'date_to' => ['nullable','date'],
            'min_points' => ['nullable','integer'],
            'max_points' => ['nullable','integer'],
            'has_expired' => ['nullable','boolean'],
            'branch_id' => ['nullable','integer'],
            'place_id' => ['nullable','integer'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:200'],
        ];
    }
}
