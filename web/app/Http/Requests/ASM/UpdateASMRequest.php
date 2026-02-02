<?php

namespace App\Http\Requests\ASM;

use Illuminate\Foundation\Http\FormRequest;

class UpdateASMRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->id = $this->route('asm');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return[
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10|unique:users,phone,'.$this->id,
            'email' => 'required|email|unique:users,email,'.$this->id,
            'branch_ids' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return[
            "branch_ids.array" => "Invalid Branch Input"
        ];
    }
}
