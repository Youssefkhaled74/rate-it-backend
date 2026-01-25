<?php

namespace App\Modules\Vendor\Branches\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'place_id' => 'required|integer|exists:places,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9+\-\s()]+$/|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'working_hours' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'place_id.required' => __('vendor.branch.place_id_required'),
            'place_id.exists' => __('vendor.branch.place_id_invalid'),
            'name.required' => __('vendor.branch.name_required'),
            'phone.required' => __('vendor.branch.phone_required'),
            'phone.regex' => __('vendor.branch.phone_invalid'),
            'address.required' => __('vendor.branch.address_required'),
        ];
    }
}
