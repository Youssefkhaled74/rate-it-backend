<?php

namespace App\Modules\User\Profile\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() != null;
    }

    public function rules()
    {
        $id = $this->user()->id;
        return [
            'name' => 'sometimes|string|min:2|max:255',
            'email' => [ 'sometimes','email', Rule::unique('users','email')->ignore($id) ],
            'avatar' => 'sometimes|file|mimes:jpg,jpeg,png,webp|max:2048'
        ];
    }
}
