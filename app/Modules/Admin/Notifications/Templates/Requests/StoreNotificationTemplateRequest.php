<?php

namespace App\Modules\Admin\Notifications\Templates\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class StoreNotificationTemplateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'key' => 'nullable|string|max:191|alpha_dash|unique:notification_templates,key',
            'type' => 'nullable|string|max:191',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'body_en' => 'required|string',
            'body_ar' => 'required|string',
            'variables_schema' => 'nullable|array',
            'channel' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ];
    }
}
