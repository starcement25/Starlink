<?php

namespace App\Http\Requests\Mason;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class PointManupulateRequest extends FormRequest
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

    public function messages()
    {
        return [
            'user_disable.required_if' => 'The user disable field is required when action type is point deduct.',
            'description.required_if' => 'The description field is required when user disable field is yes.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'point' => 'required|numeric',
            'type' => 'required'
        ];

        // dd($this->user);
        $user = User::find($this->user);

        if( !empty($user->status) && $user->status == 1)
        {
            $rules['user_disable'] = 'required_if:type,2|nullable|integer';
            $rules['description'] = 'required_if:user_disable,1|nullable|string';
        }

        return $rules;
    }
}
