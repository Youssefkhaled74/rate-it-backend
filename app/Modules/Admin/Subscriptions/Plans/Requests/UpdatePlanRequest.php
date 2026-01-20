<?php

namespace App\Modules\Admin\Subscriptions\Plans\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class UpdatePlanRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'code' => 'nullable|string|max:191|unique:subscription_plans,code,'.$id,
            'name_en' => 'required|string|max:191',
            'name_ar' => 'nullable|string|max:191',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price_cents' => 'required|integer|min:0',
            'currency' => 'nullable|string|max:6',
            'interval' => 'nullable|string|max:16',
            'interval_count' => 'nullable|integer|min:1',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
