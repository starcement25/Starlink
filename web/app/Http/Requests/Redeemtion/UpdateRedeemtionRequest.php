<?php

namespace App\Http\Requests\Redeemtion;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class UpdateRedeemtionRequest extends FormRequest
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
        $rules = [
            'status' => 'required',
        ];
        if($this->status == 1)
        {
            $additionalRule = [
                'delivery_date'   => 'required',
            ];
            $rules+= $additionalRule;
        }
        if(\Auth::user()->role === User::$adminRole && $this->status == 1)
        {
            $additionalRule = [
                'address1'   => 'required',
                'address2'   => 'required',
                'city'   => 'required',
                'district'   => 'required',
                'state'   => 'required',
                'country'   => 'required',
                'pincode'   => 'required|min_digits:6|max_digits:6|integer',
            ];
            $rules+= $additionalRule;
        }
        return $rules;
    }
}
