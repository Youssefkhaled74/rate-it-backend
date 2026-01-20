<?php

namespace App\Modules\Admin\Notifications\Templates\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class AdminTemplatesIndexRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => 'integer|min:1|max:200',
            'is_active' => 'nullable|boolean',
            'q' => 'nullable|string|max:255',
        ];
    }
}
