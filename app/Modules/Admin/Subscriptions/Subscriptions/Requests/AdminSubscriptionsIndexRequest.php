<?php

namespace App\Modules\Admin\Subscriptions\Subscriptions\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class AdminSubscriptionsIndexRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'plan_id' => 'nullable|integer',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'q' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:200',
            'sort' => 'nullable|in:created_at',
            'direction' => 'nullable|in:asc,desc',
        ];
    }
}
