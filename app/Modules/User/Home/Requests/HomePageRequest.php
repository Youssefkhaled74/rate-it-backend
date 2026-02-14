<?php

namespace App\Modules\User\Home\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ];
    }

    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 10);
    }
}
