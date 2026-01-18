<?php

namespace App\Modules\User\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'session_token' => 'required|string',
            'overall_rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'answers' => 'required|json',
            'photos' => 'sometimes|array|max:3',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'photos.max' => trans('reviews.max_photos'),
        ];
    }
}
