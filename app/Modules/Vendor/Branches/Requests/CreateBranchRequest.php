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
            'name' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'name_ar' => 'nullable|string|max:255',
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
            'phone.required' => __('vendor.branch.phone_required'),
            'phone.regex' => __('vendor.branch.phone_invalid'),
            'address.required' => __('vendor.branch.address_required'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $nameEn = $this->input('name_en');
        $nameAr = $this->input('name_ar');
        $name = $this->input('name');

        if (empty($nameEn) && !empty($name)) {
            $this->merge(['name_en' => $name]);
        }
        if (empty($name) && !empty($nameEn)) {
            $this->merge(['name' => $nameEn]);
        }
        if (empty($name) && empty($nameEn) && !empty($nameAr)) {
            $this->merge(['name' => $nameAr, 'name_en' => $nameAr]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $name = trim((string) $this->input('name', ''));
            $nameEn = trim((string) $this->input('name_en', ''));
            $nameAr = trim((string) $this->input('name_ar', ''));
            if ($name === '' && $nameEn === '' && $nameAr === '') {
                $validator->errors()->add('name_en', __('vendor.branch.name_required'));
            }
        });
    }
}
