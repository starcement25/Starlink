<?php

namespace App\Http\Requests\StarSaathi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApproveLiftingRequest extends FormRequest
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
        $rule = [
            'lifting_id' => 'required|integer'
        ];
        if($this->has('qty'))
        {
            $rule += ['qty' => 'required|integer|gt:0'];
        }
        return $rule;
    }

    public function messages()
    {
        return [
            'lifting_id.required' => 'Lifting Id is Required',
            'qty.required' => 'Quantity is Required',
            'qty.integer' => 'Quantity should not be pointed value.',
            'qty.gt' => 'Quantity must be greater than 0.',
        ];
    }

    public function failedValidation(Validator $validator) {

        // $errors = (new ValidationException($validator))->errors();
         $errors = $validator->errors();
           throw new HttpResponseException(response()->json(
            [
                'status' => false,
                'msg' => $errors->first(),
                'data'=> [],
           ]
           , 200));
           
    }
}
