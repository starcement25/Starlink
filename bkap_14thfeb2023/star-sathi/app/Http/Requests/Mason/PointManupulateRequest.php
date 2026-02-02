<?php

namespace App\Http\Requests\Mason;

use Illuminate\Foundation\Http\FormRequest;

class PointManupulateRequest extends FormRequest
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
        return [
           'point' => 'required|numeric',
           'type' => 'required',
        
        ];
    }
}
