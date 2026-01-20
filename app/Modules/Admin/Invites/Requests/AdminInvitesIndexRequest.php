<?php

namespace App\Modules\Admin\Invites\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class AdminInvitesIndexRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,joined,rejected,expired',
            'q' => 'nullable|string|max:255',
            'inviter_id' => 'nullable|integer',
            'invitee_user_id' => 'nullable|integer',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'sort' => 'nullable|in:created_at,status',
            'direction' => 'nullable|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:200',
        ];
    }
}
