<?php

namespace App\Http\Requests\Dealer;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateDealerRequest extends FormRequest
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
        return  [
      
            'name' => 'required|max:255',
            'emp_code'=>'required|unique:users,emp_code',
            'role' => 'required',
            'linked_dealer' => 'nullable',
            'branch_id' => 'required',
            'status' => 'required',
            'phone' => 'required|unique:users,phone|digits:10',
            'whatsapp_no' => 'required'
        ];
    }
}
