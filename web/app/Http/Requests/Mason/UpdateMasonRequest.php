<?php

namespace App\Http\Requests\Mason;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMasonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->id = $this->route('mason');
        return true;
    }

    public function messages()
    {
        return [
            'disable_reason.required_if' => 'The disable reason field is required when status is disable.'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        // $rules = User::$masonUpdateRules;
        
        // return $rules;
        return[
                'name' => 'required|string|max:255',
                'branch_id' => 'required',
                'phone' => 'required|digits:10|unique:users,phone,'.$this->id,
                'spouse_name' => 'required_if:marital_status,1|nullable|string',
                'spouse_dob' => 'required_if:marital_status,1|nullable|date',
                'marital_status' => 'required',
                'address' => 'string',
                'parent' => 'required|numeric',
                'dealers' => 'required',
                'dob' => 'date',
                'aadhaar_no' => 'required|digits:12|unique:users,aadhaar_no,'.$this->id,
                //'aadhaar_no' => 'required|digits:12',
                'status' => 'required|integer',
                'disable_reason' => 'required_if:status,0|nullable|string',
             ];   
    }
}
