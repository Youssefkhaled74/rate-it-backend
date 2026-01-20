<?php

namespace App\Modules\Admin\Subscriptions\Plans\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class PlansIndexRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => 'nullable|integer|min:1|max:200',
            'q' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort' => 'nullable|in:created_at',
            'direction' => 'nullable|in:asc,desc',
        ];
    }
}
