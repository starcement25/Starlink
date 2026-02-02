<?php

namespace App\Http\Requests\DealerLinkings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\GoogleTranslateService;
use App\Utils\LocalLanguageTranslation;

class DealerLinkingRequest extends FormRequest
{
    protected $googleTranslate;
    protected $localLanguageTranslate;
    protected $targetLanguage = null;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }
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
            'dealer_ids' => 'required|array',
            'dealer_ids.*' => 'integer',
        ];
        return $rule;
    }

    public function messages()
    {
        return [
            'dealer_ids.*.integer' => 'Dealer Ids should be intergers',
        ];
    }

    public function failedValidation(Validator $validator) {
        
        if(!empty($this->preferred_app_lang))
        {
            $targetLanguage = $this->preferred_app_lang;
        }
        if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
        {
            $targetLanguage = \Auth::user()->preferred_app_lang;
        }
        // $errors = (new ValidationException($validator))->errors();
         $errors = $validator->errors();
           throw new HttpResponseException(response()->json(
            [
                'status' => false,
                'msg' => $this->googleTranslate->translateText($errors->first(), $targetLanguage),
                'data'=> [],
           ]
           , 200));

    }
}
