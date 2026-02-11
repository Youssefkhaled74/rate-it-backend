<?php

namespace App\Modules\Vendor\Branches\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|string|max:255',
            'phone' => 'sometimes|regex:/^[0-9+\-\s()]+$/|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'sometimes|string|max:500',
            'working_hours' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('vendor.branch.name_required'),
            'phone.regex' => __('vendor.branch.phone_invalid'),
            'address.string' => __('vendor.branch.address_required'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $nameEn = $this->input('name_en');
        $nameAr = $this->input('name_ar');
        $name = $this->input('name');

        if ($this->has('name') && empty($nameEn) && !empty($name)) {
            $this->merge(['name_en' => $name]);
        }
        if ($this->has('name_en') && empty($name) && !empty($nameEn)) {
            $this->merge(['name' => $nameEn]);
        }
        if ($this->has('name_ar') && !$this->has('name') && !$this->has('name_en') && !empty($nameAr)) {
            $this->merge(['name' => $nameAr, 'name_en' => $nameAr]);
        }
    }
}
