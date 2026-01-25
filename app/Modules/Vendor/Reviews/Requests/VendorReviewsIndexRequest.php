<?php

namespace App\Modules\Vendor\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorReviewsIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'min_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'max_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'has_photos' => ['nullable', 'boolean'],
            'keyword' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ];
    }
}
