<?php

namespace App\Http\Requests\ASM;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateASMRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'phone' => 'required|digits:10|integer|unique:users,phone',
            'email' => 'required|email|unique:users,email',
        ];
    }
}
