<?php

namespace App\Http\Requests\Mason;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateMasonRequest extends FormRequest
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
                'branch_id' => 'required',
                'phone' => 'required|digits:10|integer',
                'marital_status' => 'required',
                'address1' => 'required|string',
                'address2' => 'required|string',
                'state'=> 'required',
                'city'=> 'required',
                'district'=> 'required',
                'country'=> 'required',
                'pincode'=> 'required|digits:6|integer',
                'spouse_name' => 'required_if:marital_status,1|nullable|string',
                'spouse_dob' => 'required_if:marital_status,1|nullable|date',
                'parent' => 'required|numeric',
                'dealers' => 'required', 
                'dob' => 'required|date',
                'aadhaar_no' => 'required|digits:12|unique:users,aadhaar_no'
            ];
    }
}
