<?php

namespace App\Modules\Admin\Dashboard\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class DashboardChartRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'interval' => 'nullable|in:day,week,month',
            'limit' => 'nullable|integer|min:1|max:50',
            'metric' => 'nullable|in:reviews_count,avg_rating,points_issued',
            'min_reviews' => 'nullable|integer|min:1',
            'place_id' => 'nullable|integer|exists:places,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'category_id' => 'nullable|integer|exists:categories,id',
        ];
    }
}

