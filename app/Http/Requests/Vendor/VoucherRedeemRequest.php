<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRedeemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_or_link' => ['required', 'string', 'max:255'],
            'branch_id' => ['nullable', 'integer'],
        ];
    }
}
