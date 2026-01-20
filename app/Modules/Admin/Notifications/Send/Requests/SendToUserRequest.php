<?php

namespace App\Modules\Admin\Notifications\Send\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class SendToUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'template_id' => 'nullable|integer|exists:notification_templates,id',
            'lang' => 'nullable|in:en,ar,auto',
            'data' => 'nullable|array',
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'body_en' => 'nullable|string',
            'body_ar' => 'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $d = $this->all();
            if (empty($d['template_id'])) {
                if (empty($d['title_en']) || empty($d['body_en']) || empty($d['title_ar']) || empty($d['body_ar'])) {
                    $v->errors()->add('template_id','Either template_id or full title/body in both languages is required.');
                }
            }
        });
    }
}
