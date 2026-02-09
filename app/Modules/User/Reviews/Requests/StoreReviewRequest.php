<?php

namespace App\Modules\User\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\RatingCriteriaChoice;

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
            // answers may be submitted as nested form array OR as JSON string (backwards compatibility)
            'answers' => ['required'],
            'photos' => ['nullable','array','max:3'],
            'photos.*' => ['file','mimes:jpg,jpeg,png,webp','max:2048'],
            'answer_photos' => ['nullable','array'],
            'answer_photos.*' => ['nullable','array'],
            'answer_photos.*.*' => ['file','mimes:jpg,jpeg,png,webp','max:2048'],
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
            $answersInput = $this->input('answers');

            // Support old JSON string input for backward compatibility
            if (is_string($answersInput)) {
                $decoded = json_decode($answersInput, true);
                if (! is_array($decoded)) {
                    $validator->errors()->add('answers', trans('reviews.invalid_answers_json'));
                    return;
                }
                $answers = $decoded;
            } else {
                $answers = is_array($answersInput) ? $answersInput : null;
            }

            if (! is_array($answers) || count($answers) < 1) {
                $validator->errors()->add('answers', trans('reviews.invalid_answers_json'));
                return;
            }

            // Try to resolve subcategory from provided session_token to ensure criteria belong to branch
            $subCategoryId = null;
            $sessionToken = $this->input('session_token');
            if ($sessionToken) {
                $session = \App\Models\BranchQrSession::where('session_token', $sessionToken)->first();
                $subCategoryId = optional(optional($session)->branch)->brand ? optional($session->branch->brand)->subcategory_id : null;
            }

            foreach ($answers as $i => $ans) {
                $idx = "answers.{$i}";
                $criteriaId = isset($ans['criteria_id']) ? (int) $ans['criteria_id'] : null;
                if (! $criteriaId) {
                    $validator->errors()->add($idx . '.criteria_id', trans('reviews.invalid_answer'));
                    continue;
                }

                // If we can determine subcategory, ensure criteria belongs to that subcategory
                if ($subCategoryId) {
                    $exists = DB::table('rating_criteria')->where('id', $criteriaId)->where('subcategory_id', $subCategoryId)->exists();
                    if (! $exists) {
                        $validator->errors()->add($idx . '.criteria_id', trans('reviews.invalid_answer'));
                        continue;
                    }
                } else {
                    // Fallback: ensure criteria exists globally
                    if (! DB::table('rating_criteria')->where('id', $criteriaId)->exists()) {
                        $validator->errors()->add($idx . '.criteria_id', trans('reviews.invalid_answer'));
                        continue;
                    }
                }
                

                $hasRating = array_key_exists('rating_value', $ans) && $ans['rating_value'] !== null && $ans['rating_value'] !== '';
                $hasYesNo = array_key_exists('yes_no_value', $ans) && $ans['yes_no_value'] !== null && $ans['yes_no_value'] !== '';
                $hasChoice = array_key_exists('choice_id', $ans) && $ans['choice_id'] !== null && $ans['choice_id'] !== '';
                $hasText = array_key_exists('text_value', $ans) && is_string($ans['text_value']) && trim($ans['text_value']) !== '';
                $hasPhoto = array_key_exists('has_photo', $ans) && in_array($ans['has_photo'], [1,'1',true], true);

                $provided = intval($hasRating) + intval($hasYesNo) + intval($hasChoice) + intval($hasText) + intval($hasPhoto);
                if ($provided !== 1) {
                    $validator->errors()->add($idx, trans('reviews.invalid_answer'));
                    continue;
                }

                if ($hasRating) {
                    $v = $ans['rating_value'];
                    if (! is_numeric($v) || (int)$v < 1 || (int)$v > 5) {
                        $validator->errors()->add($idx . '.rating_value', trans('reviews.invalid_answer'));
                    }
                }

                if ($hasYesNo) {
                    $v = $ans['yes_no_value'];
                    if (! in_array($v, [0,1,'0','1',true,false], true)) {
                        $validator->errors()->add($idx . '.yes_no_value', trans('reviews.invalid_answer'));
                    }
                }

                if ($hasChoice) {
                    $choiceId = (int) $ans['choice_id'];
                    $exists = RatingCriteriaChoice::where('id', $choiceId)->where('criteria_id', $criteriaId)->exists();
                    if (! $exists) {
                        $validator->errors()->add($idx . '.choice_id', trans('reviews.invalid_answer'));
                    }
                }

                if ($hasText) {
                    $v = trim((string) $ans['text_value']);
                    if ($v === '' || mb_strlen($v) > 1000) {
                        $validator->errors()->add($idx . '.text_value', trans('reviews.invalid_answer'));
                    }
                }

                if ($hasPhoto) {
                    $files = $this->file('answer_photos', []);
                    $criteriaFiles = is_array($files) ? ($files[$criteriaId] ?? []) : [];
                    if (! is_array($criteriaFiles) || count($criteriaFiles) === 0) {
                        $validator->errors()->add($idx . '.has_photo', trans('reviews.invalid_answer'));
                    }
                }
            }

            // validate photos count as extra safety
            $files = $this->file('photos', []);
            if (is_array($files) && count($files) > 3) {
                $validator->errors()->add('photos', trans('reviews.max_photos'));
            }

            // Merge parsed answers (array) back into request so controller receives array
            $this->merge(['answers' => $answers]);
        });
    }
}
