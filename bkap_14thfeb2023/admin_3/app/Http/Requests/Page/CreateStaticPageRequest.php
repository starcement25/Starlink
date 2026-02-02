<?php

namespace App\Http\Requests\Page;

use App\Models\StaticPage;
use Illuminate\Foundation\Http\FormRequest;

class CreateStaticPageRequest extends FormRequest
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
        return StaticPage::$rules;
    }
}
