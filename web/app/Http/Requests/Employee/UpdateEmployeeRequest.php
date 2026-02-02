<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    private $id;

    public function authorize()
    {
        $this->id = $this->route('employee');
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
            'name'=>'required',
            'email'=>'required',
            'emp_code'=>'required|unique:users,emp_code,'.$this->id,
            // 'address'=>'required',
            'status'=>'required',
            'designation'=>'required',
            'phone'=>'required|digits:10|unique:users,phone,'.$this->id,
            'branch_id'=>'required',
        ];
    }
}
