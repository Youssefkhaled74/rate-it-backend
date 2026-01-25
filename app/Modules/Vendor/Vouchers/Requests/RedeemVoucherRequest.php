<?php

namespace App\Modules\Vendor\Vouchers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedeemVoucherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code_or_link' => ['required', 'string', 'max:500'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }

    public function messages()
    {
        return [
            'code_or_link.required' => trans('vendor.vouchers.code_required'),
            'branch_id.exists' => trans('validation.exists'),
        ];
    }
}
