<?php

namespace App\Support\Api\FormRequest;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method array validated($key = null, $default = null)
 * @method array all($keys = null)
 * @method mixed route($param = null, $default = null)
 */
class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }
}
