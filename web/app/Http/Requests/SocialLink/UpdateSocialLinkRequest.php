<?php

namespace App\Http\Requests\SocialLink;

use App\Models\SocialLink;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialLinkRequest extends FormRequest
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
        return SocialLink::$rules ;
    }

   
    public function messages()
    {
        return SocialLink::$messages ;
    }
}
