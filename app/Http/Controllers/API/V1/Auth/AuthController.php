<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\MobileVerification;
use App\Models\User;
use App\Models\UserDisableHistory;
use App\Services\GoogleTranslateService;
use App\Utils\LocalLanguageTranslation;
use Auth;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;
use Mail;
use Session;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }
    public function register(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'name' => 'required|max:55',
            'phone' => 'required|unique:users,phone|min:10|max:10',
            'role' => 'required'
         );
       

        // validation 
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $res['msg']]);
        }
        $validatedData = $validator->validated();

        if($request->role == 1)
        {
            $user = User::create($validatedData);
        }
        else
        {
            return response()->json([ 'status'=>false, 'msg' => 'Only Technique Engineer register']);
            $user = NULL;
        }

        $this->sendOTP($request);
        return response()->json(['user' => $user->phone, 'status'=>true, 'msg' => " Register Successfully please verify by OTP sent to registered number"]);
    }

    public function login(Request $request)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        $input = $request->all();
        $rules = array(
            'phone' => 'required|min:10|max:10|exists:users,phone',
            'otp' => 'required',
            'fcm_token'=> 'nullable'
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $res['msg']], 404);
        }
    
        $loginData = $validator->validated();
           $user1 = User::where('phone', $request->phone)->where('status', 1)->first();
           if(!$user1)
           {
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Your_account_may_be_disabled,_please_try_with_another_credentials', $targetLanguage)], 200);
           }
                // if($user1->role == 3 || $user1->role == 4 )
                // {
                //     return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('You_can_not_login_as_Dealer_/_RSSD,_please_try_with_another_account', $targetLanguage)], 200);
                
                // }
                if(!in_array($user1->role, [1,2]))
                {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('you_are_not_allowed_to_login', $targetLanguage)], 200);
                
                }
    
        // $token = getenv("TWILIO_TOKEN");
        // $twilio_sid = getenv("TWILIO_SID");
        // $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        // try{
        //     $twilio = new Client($twilio_sid, $token);
        //     $verification = $twilio->verify->v2->services($twilio_verify_sid)->verificationChecks->create(['code' => $request->otp, 'to' => "+91".$request->phone]);
        //     if ($verification->valid) {
        //         $user = User::where('phone', $request->phone)->first();              
        //         $user->password = Hash::make($request->otp);
        //         $user->save();
        //         $credencial['password'] = $request->otp;
        //         $credencial['phone'] = $request->phone;
        //     }else{
        //         $credencial['password'] = NULL;
        //         $credencial['phone'] = NULL;
        //     }
        // }
        // catch(\Exception $e ){
        //     return response()->json(['status' => false, 'msg' => 'Invalid OTP'], 200);
        // }
    
        $credencial['password'] = $request->otp;
        $credencial['phone'] = $request->phone;
        if (!auth()->attempt($credencial)) {
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Wrong_OTP._Please_check_phone_number/OTP_and_try_again', $targetLanguage)], 200);
        }
        // $requestedUserRec = User::where("phone", $request->phone)->first();
        // if (!$requestedUserRec || !(\Hash::check($request->otp, $requestedUserRec->password))) {
        //     return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Wrong_OTP._Please_check_phone_number/OTP_and_try_again', $targetLanguage)], 200);
        // }
        //Delete Existing All Tokens
        $existingTokens = Token::where('user_id', $user1->id)->get();
        foreach($existingTokens as $existingToken)
        {
            $existingToken->delete();
        }
        //Create New Token
        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        // $accessToken = $requestedUserRec->createToken('authToken')->accessToken;
        
        $user1->last_login_date_time= Carbon::now();
        $user1->login_device_type=$request->device_type;
        $user1->login_device_name=$request->device_name;
        $user1->app_version=$request->app_version;
        $user1->login_status=1;
        $user1->save();
        $existingTokens = Token::where('user_id', $user1->id)->get();
        if(count($existingTokens) > 1)
        {
            $i=1;
            foreach($existingTokens as $existingToken)
            {
                
                if($i == 1)
                {
                    continue;
                }
                $existingToken->delete();
                $i++;
            }
        }
        $userData = getProfile(auth()->user()->id);
        if(empty($targetLanguage) && !empty($userData['preferred_app_lang'] ?? null))
        {
            $targetLanguage = $userData['preferred_app_lang'];
        }
        
        // $userData['name'] = $this->googleTranslate->translateText($userData['name'], $targetLanguage);
        // $userData['role_name'] = $this->googleTranslate->translateText($userData['role_name'], $targetLanguage);
        // $userData['address'] = $this->googleTranslate->translateText($userData['address'], $targetLanguage);
        // $userData['address1'] = $this->googleTranslate->translateText($userData['address1'], $targetLanguage);
        // $userData['address2'] = $this->googleTranslate->translateText($userData['address2'], $targetLanguage);
        // $userData['city'] = $this->googleTranslate->translateText($userData['city'], $targetLanguage);
        // $userData['country'] = $this->googleTranslate->translateText($userData['country'], $targetLanguage);
        // $userData['designation'] = $this->googleTranslate->translateText($userData['designation'], $targetLanguage);
        // $userData['district'] = $this->googleTranslate->translateText($userData['district'], $targetLanguage);
        // $userData['spouse_name'] = $this->googleTranslate->translateText($userData['spouse_name'], $targetLanguage);
        $responseMsg = $this->localLanguageTranslate->translate('Log_in_successfull', $targetLanguage);

        if(!empty($request->fcm_token)){
            auth()->user()->update([
                'fcm_token' => $request->input('fcm_token')
            ]);
        }


        return response()->json(['status' => true , 'data' => $userData, 'access_token' => $accessToken, 'msg' => $responseMsg], 200);
    }

    public function testLogin(Request $request)
    {
     
        $input = $request->all();
        $rules = array(
            'phone' => 'required|min:10|max:10|exists:users,phone',
            'otp' => 'required'
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $res['msg']], 200);
        }
    
        $loginData = $validator->validated();
           $user1 = User::where('phone', $request->phone)->where('status', 1)->first();
           if(!$user1)
           {
            return response()->json(['status' => false, 'msg' => 'You may be disabled, please try with another credentials.'], 200);
           }
                if($user1->role == 3 || $user1->role == 4 )
                {
                    return response()->json(['status' => false, 'msg' => 'You can not login as Dealer / RSSD, please try with another credentials.'], 200);
                
                }
    
      
        try{
           
            if (1) {
                $user = User::where('phone', $request->phone)->first();              
                $user->password = Hash::make("1234");
                $user->save();
                $credencial['password'] = $request->otp;
                $credencial['phone'] = $request->phone;
            }else{
                $credencial['password'] = NULL;
                $credencial['phone'] = NULL;
            }
        }
        catch(\Exception $e ){
            return response()->json(['status' => false, 'msg' => 'Invalid OTP'], 200);
        }
    
      
        if (!auth()->attempt($credencial)) {
            return response()->json(['status' => false, 'msg' => 'Invalid credentials. Please check Phone/OTP and try again'], 200);
        }
        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        // $requestedUserRec = User::where("phone", $request->phone)->first();
        // if (!$requestedUserRec || !(\Hash::check($request->otp, $requestedUserRec->password))) {
        //     return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Wrong_OTP._Please_check_phone_number/OTP_and_try_again', $targetLanguage)], 200);
        // }
        // $accessToken = $requestedUserRec->createToken('authToken')->accessToken;
        
        $user->login_device_type=$request->device_type;
        $user->login_device_name=$request->device_name;
        $user->app_version=$request->app_version;
        $user->login_status=1;
        $user->save();
        return response()->json(['status' => true , 'data' => getProfile(auth()->user()->id), 'access_token' => $accessToken, 'msg' => 'login successfully'], 200);
    }
    
    public function sendOTP(Request $request)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        $input = $request->all();
        $rules = array(
            'phone' => 'required|min:10|max:10|exists:users,phone',
         );
        $messages = array(
            "phone.exists" => "Phone Number is not Registered." 
        );
        $attributes = array(
            "phone" => "Phone Number" 
        );
        // validation 
        $validator  = Validator::make($input, $rules, $messages, $attributes);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $this->googleTranslate->translateText($res['msg'], $targetLanguage)]);
        }
        $loginData = $validator->validated();
        $phone = $loginData['phone'];
        $user = User::where('phone', $phone)->first();
        if($user)
        {
            if(empty($targetLanguage) && !empty($user->preferred_app_lang))
            {
                $targetLanguage = $user->preferred_app_lang;
            }

            //If user is Inactive
            if($user->status != 1)
            {
                return response()->json(['status'=>false, 'msg' => $this->localLanguageTranslate->translate("Your_account_may_be_disabled,_please_try_with_another_credentials", $targetLanguage)]);
            }
            $twr = $this->sendSMS($phone, $request);
            //$twr =1;
            if($twr["status"])
            {
                return response()->json(['status'=>true,'msg' => $this->localLanguageTranslate->translate("OTP_sent_successfully", $targetLanguage), 'otp_purpose' => $request->otp_purpose]);
            }else
            {
                return response()->json(['status'=>false, 'msg' => $this->googleTranslate->translateText($twr["msg"], $targetLanguage)]);
            } 
        }else
        {
            return response()->json(['status'=>false, 'msg' => $this->localLanguageTranslate->translate("Phone_number_has_not_registered", $targetLanguage)]);
        }
        
    }
    public function sendOTPToNewNumber(Request $request)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        $input = $request->all();
        $rules = array(
            'phone' => 'required|min:10|max:10',
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $this->googleTranslate->translateText($res['msg'], $targetLanguage)], 200);
        }
        $loginData = $validator->validated();
        $phone = $loginData['phone'];
        if(empty($targetLanguage) && \Auth::check() && !empty(\Auth::user()->preferred_app_lang))
        {
            $targetLanguage = \Auth::user()->preferred_app_lang;
        }
        $twr = $this->sendSMSToNewNumber($phone, $request->otp_purpose);
        if($twr["status"]) {
            return response()->json(['status'=>true,'msg' => $this->localLanguageTranslate->translate("OTP_sent_successfully", $targetLanguage), 'otp_purpose' => $request->otp_purpose], 200);
        }else {
            return response()->json(['status'=>false, 'msg' => $this->googleTranslate->translateText($twr["msg"], $targetLanguage)], 200);
        }   
    }
    public function sendSMS($receiverNumber, Request $request)
    {
        
        //return $tmp;
        
        // $token = getenv("TWILIO_TOKEN");
        // $twilio_sid = getenv("TWILIO_SID");
        // $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        // $phone = "+91".$receiverNumber;
        try{
            // $twilio = new Client($twilio_sid, $token);
            // $verify = $twilio->verify->v2->services($twilio_verify_sid)
            // ->verifications
            // ->create($phone, 'sms');
            $user = User::where('phone', $receiverNumber)->first();
            //otp_type 1 means dynamic
            if($user->otp_type == 1)
            {
                $otp = $this->generateNumericOTP(4);
            }
            else
            {
                $otp = 1234;
            }
            // if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
            // {
            //     $msg = $this->googleTranslate->translateText("Your Star Link verification code is: ", $request->preferred_app_lang).$otp.$this->googleTranslate->translateText(". STAR CEMENT", $request->preferred_app_lang);
            // }
            // else if(!empty($user->preferred_app_lang))
            // {
            //     $msg = $this->googleTranslate->translateText("Your Star Link verification code is: ", $user->preferred_app_lang).$otp.$this->googleTranslate->translateText(". STAR CEMENT", $user->preferred_app_lang);
            // }
            // else
            // {
                $msg = "Your Star Link verification code is: ".$otp.". STAR CEMENT";
            // }
            if($user->otp_type == 1)
            {
                $tmp = Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$receiverNumber.'&from=STARCM&text='.$msg.'&dlr-mask=19&dlr-url');
            }
            $user->password = Hash::make($otp);
            $user->save();
            return ["status" => true, "msg" => ""];
        }
        catch(\Exception $e ){
            // return response()->json($e->getMessage());
            return ["status" => false, "msg" => $e->getMessage()];
        }
    }
    public function sendSMSToNewNumber($receiverNumber, $otp_purpose)
    {
        
        //return $tmp;

        // $token = getenv("TWILIO_TOKEN");
        // $twilio_sid = getenv("TWILIO_SID");
        // $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        // $phone = "+91".$receiverNumber;
        try{
            // $twilio = new Client($twilio_sid, $token);
            // $verify = $twilio->verify->v2->services($twilio_verify_sid)
            // ->verifications
            // ->create($phone, 'sms');
            $user = MobileVerification::where('phone', $receiverNumber)->first();
            $openTime = Carbon::now(); // 02:00:00
            $closeTime = Carbon::parse($openTime)->addMinutes(15);
            if(empty($user))
            {
                $user = new MobileVerification();
                $user->phone = $receiverNumber;
                $user->valid_upto = $closeTime;      
                $user->save();
            } 
            $user = MobileVerification::where('phone', $receiverNumber)->first();
            //------------otp purposes--------------------
            //formData.append('otp_purpose',"mason registration")
            //  formData.append("otp_purpose","add_lifting")
            // formData.append('otp_purpose', "gift_redemption");
            if($user->otp_type != 1)
            {
                $otp = 1234;
            }
            else
            {
                $otp = $this->generateNumericOTP(4);
                $msg = "Your Star Link verification code is: ".$otp.". STAR CEMENT";
                $tmp = Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$receiverNumber.'&from=STARCM&text='.$msg.'&dlr-mask=19&dlr-url');
            }
            $user->otp = $otp;
            $user->save();
            
            return ["status" => true, "msg" => ""];
        }
        catch(\Exception $e ){
            return ["status" => false, "msg" => $e->getMessage()];
        }
    }
    function logout(Request $request)
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
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $user->login_status=0;
        $user->save();
        $request->user()->token()->revoke();
        $re_message = $this->localLanguageTranslate->translate('Successfully_logged_out', $targetLanguage);
        return response()->json(['status' => true ,'message' => $re_message]);
    }

    function deleteAccount(Request $request)
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
        $userId = Auth::user()->id;
        $user = User::find($userId);
        if($user)
        {
            //comment out as per request 05-05-2026
            // $user->status = 0;
            // $user->save();
            //$request->user()->token()->revoke();

            // Save the account deletion log
             UserDisableHistory::create([
                    "user_id" => $userId,
                    "disable_date_time" => Carbon::now(),
                    "disable_reason" => 'User Has Deleted Account From App.',
                    "point_deducted" => 0,
            ]);

            return response()->json(['status'=>true,'msg' => $this->localLanguageTranslate->translate("Account_deleted_successfully", $targetLanguage)], 200);
        }
        else
        {
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('User_Not_Found', $targetLanguage)], 200);
        }
        
    }

    function generateNumericOTP($n) 
    {
        $generator = "1357902468";
        $result = "";
        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand()%(strlen($generator))), 1);
        }
        return $result;
    }
   
}
