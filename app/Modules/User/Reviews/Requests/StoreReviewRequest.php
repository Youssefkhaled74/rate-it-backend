<?php

namespace App\Modules\User\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'session_token' => ['required','string','max:255'],
            'overall_rating' => ['required','numeric','min:1','max:5'],
            'comment' => ['nullable','string','max:2000'],
            'answers' => ['required','string'],
            'photos' => ['nullable','array','max:3'],
            'photos.*' => ['file','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'photos.max' => trans('reviews.max_photos'),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $answersRaw = (string) $this->input('answers');
            $answers = json_decode($answersRaw, true);
            if (! is_array($answers)) {
                $validator->errors()->add('answers', trans('reviews.invalid_answers_json'));
            }

            $files = $this->file('photos', []);
            if (is_array($files) && count($files) > 3) {
                $validator->errors()->add('photos', trans('reviews.max_photos'));
            }
        });
    }
}
