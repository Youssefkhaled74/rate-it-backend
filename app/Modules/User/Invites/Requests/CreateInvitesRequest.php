<?php

namespace App\Modules\User\Invites\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvitesRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() != null;
    }

    public function rules()
    {
        return [
            'phones' => 'required|array|min:1',
            'phones.*' => 'required|string',
        ];
    }
}
