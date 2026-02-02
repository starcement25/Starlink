<?php

namespace App\Http\Requests\MasonCategory;

use App\Models\MasonCategory;
use Illuminate\Foundation\Http\FormRequest;

class CreateMasonCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return MasonCategory::$rules;
    }
}
