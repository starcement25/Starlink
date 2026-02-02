<?php

namespace App\Http\Requests\Dealer;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDealerRequest extends FormRequest
{

    private $id;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->id = $this->route('dealer');
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
      
            'name' => 'required|max:255',
            'emp_code'=>'required|unique:users,emp_code,'.$this->id,
            'role' => 'required',
            'linked_dealer' => 'nullable',
            'branch_id' => 'required',
            'status' => 'required',
            'phone' => 'required|digits:10|unique:users,phone,'.$this->id,
            'whatsapp_no' => 'required'
        ];
    }
}
