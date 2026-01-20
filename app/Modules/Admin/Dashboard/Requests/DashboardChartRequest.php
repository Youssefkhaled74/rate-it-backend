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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'period' => 'nullable|in:day,week,month,year',
        ];
    }
}
