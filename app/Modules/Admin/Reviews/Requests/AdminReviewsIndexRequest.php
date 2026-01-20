<?php

namespace App\Modules\Admin\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminReviewsIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'place_id' => ['nullable','integer','exists:places,id'],
            'branch_id' => ['nullable','integer','exists:branches,id'],
            'user_id' => ['nullable','integer','exists:users,id'],
            'date_from' => ['nullable','date'],
            'date_to' => ['nullable','date'],
            'rating_min' => ['nullable','numeric'],
            'rating_max' => ['nullable','numeric'],
            'is_hidden' => ['nullable','boolean'],
            'is_featured' => ['nullable','boolean'],
            'q' => ['nullable','string'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:200'],
        ];
    }
}
