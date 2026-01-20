<?php

namespace App\Modules\Admin\Notifications\Broadcast\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class SendBroadcastRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'template_id' => 'nullable|integer|exists:notification_templates,id',
            'lang' => 'nullable|in:en,ar,auto',
            'data' => 'nullable|array',
            'audience' => 'required|array',
            'audience.type' => 'required|in:all,segment',
            'audience.segment' => 'nullable|array',
            'audience.segment.min_points' => 'nullable|integer|min:0',
            'audience.segment.max_points' => 'nullable|integer|min:0',
            'audience.segment.created_from' => 'nullable|date',
            'audience.segment.created_to' => 'nullable|date',
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
