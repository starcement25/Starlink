<?php

namespace App\Http\Requests\StarSaathi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteLiftingRequest extends FormRequest
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
        return [
            'lifting_id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'lifting_id.required' => 'Lifting Id is Required',
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
