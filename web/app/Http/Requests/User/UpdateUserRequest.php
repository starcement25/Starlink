<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    protected $id;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->id = $this->route('user');
        //dd($this->id);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // $userUpdateRules = User::$updateRules;
        // $userUpdateRules['email'] .= ','.$this->id.',';

        return [
            'email' => 'required|email|unique:users,email,'.$this->id,
            'role' => 'required|integer',
            'phone' => 'required|unique:users,phone,'.$this->id,
            'name' => 'required|string|max:255',
            'allocated_branches' => 'required',
            
         ];
        //return $userUpdateRules;
    }
    public function messages()
    {
        return [
            'allocated_branches.required'=>'At least one branch is required.',
            
        ];
    }
}
