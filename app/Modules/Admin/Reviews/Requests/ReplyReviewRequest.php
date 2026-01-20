<?php

namespace App\Modules\Admin\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplyReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reply_text' => ['required','string','max:2000'],
        ];
    }
}
