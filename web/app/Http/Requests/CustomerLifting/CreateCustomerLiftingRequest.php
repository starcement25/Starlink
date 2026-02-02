<?php

namespace App\Http\Requests\CustomerLifting;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerLiftingRequest extends FormRequest
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
            'dealer_id' => 'required',
            'product_id' => 'required',
            'year' => 'required',
            'month' => 'required',
            'status' => 'required',
            'quantity' => 'required',
           ];
    }

    public function messages()
    {
        return [
        'dealer_id.required' => 'The dealer field is required',
        'product_id' => 'The product field is required',
       ];
    }
}
