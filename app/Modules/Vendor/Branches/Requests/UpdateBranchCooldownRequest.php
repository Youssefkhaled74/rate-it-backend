<?php

namespace App\Modules\Vendor\Branches\Requests;

use App\Support\Api\FormRequest\BaseFormRequest;

class UpdateBranchCooldownRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'review_cooldown_days' => ['required', 'integer', 'min:0', 'max:365'],
        ];
    }

    public function messages(): array
    {
        return [
            'review_cooldown_days.required' => __('validation.required', ['attribute' => 'review_cooldown_days']),
            'review_cooldown_days.integer' => __('validation.integer', ['attribute' => 'review_cooldown_days']),
            'review_cooldown_days.min' => __('validation.min.numeric', ['attribute' => 'review_cooldown_days', 'min' => 0]),
            'review_cooldown_days.max' => __('validation.max.numeric', ['attribute' => 'review_cooldown_days', 'max' => 365]),
        ];
    }
}
