<?php

namespace App\Http\Requests\Lifting;

use App\Models\Lifting;
use Illuminate\Foundation\Http\FormRequest;

class CreateLiftingRequest extends FormRequest
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
        return Lifting::$rules;
    }

    public function messages()
    {
       return [
            'qty.required' => 'The quantity field is required.',
       ];
    }
}
