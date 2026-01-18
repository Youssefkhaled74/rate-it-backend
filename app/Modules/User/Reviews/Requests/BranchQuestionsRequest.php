<?php

namespace App\Modules\User\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchQuestionsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'session_token' => 'nullable|string',
        ];
    }
}
