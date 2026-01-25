<?php

namespace App\Modules\Vendor\Vouchers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckVoucherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code_or_link' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages()
    {
        return [
            'code_or_link.required' => trans('vendor.vouchers.code_required'),
        ];
    }
}
