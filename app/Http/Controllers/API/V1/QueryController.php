<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Query;
use App\Models\ContactPage;
use Exception;
use Mail;
use App\Mail\DemoMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Utils\LocalLanguageTranslation;
use App\Services\GoogleTranslateService;
class QueryController extends Controller
{
    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }

    function getAllQuery(Request $request)
    {
        $queries = Query::orderBy('created_at','DESC')->get();
        return response()->json(['status'=> true, 'data' => $queries, 'msg' => "Branch data fetch successfully"], 200);
    }
    function addQuery(Request $request)
    {   
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
        {
            $targetLanguage = \Auth::user()->preferred_app_lang;
        }
            $input = $request->all();
            $rules = array(
                        // 'email' => 'required|email',
                        // 'name' => 'required',
                        'message' => 'required',
                    );
            $validator  = Validator::make($input, $rules);
            $validRes = validateInput($validator);
            if ($validRes['status'] == false) {
                return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($validRes['msg'], $targetLanguage)]);
            }
           
            $data = $validRes['validated_data'];
            $data['user_id'] = \Auth::user()->id ?? null ;
            Query::create($data);
            //$headers = "Content-type: text/html\r\n"; 
            $contactPage = ContactPage::get();
            $email = $contactPage[0]->email;
            $msg = $input['message'];

            $mailData = [
                'msg' => $msg,
                'mason_email' => \Auth::user()->email ?? null,
                'mason_name' => \Auth::user()->name ?? null,
                'mason_phone' => \Auth::user()->phone ?? null,
            ];
             
            Mail::to($email)->send(new DemoMail($mailData));

            //mail($input['email'] ,"Query Mail", $msg, $headers);
            
           // echo "Basic Email Sent. Check your inbox.";
          //  mail("stalukdar011@gmail.com","Query Mail",$msg, $headers);
            return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Query_registered,_we_will_back_to_you_soon", $targetLanguage)], 200);
    }
}

