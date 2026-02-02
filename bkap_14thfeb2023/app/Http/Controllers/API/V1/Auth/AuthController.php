<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Mail;
use Auth;
use Session;
use Twilio\Rest\Client;
class AuthController extends Controller
{
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
        }else
        {
            return response()->json([ 'status'=>false, 'msg' => 'Only Technique Engineer register']);
            $user = NULL;
        }

        $this->sendOTP($request);
        return response()->json(['user' => $user->phone, 'status'=>true, 'msg' => " Register Successfully please verify by OTP sent to registered number"]);
    }

    public function login(Request $request)
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

        $token = getenv("TWILIO_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        try{
            $twilio = new Client($twilio_sid, $token);
            $verification = $twilio->verify->v2->services($twilio_verify_sid)->verificationChecks->create(['code' => $request->otp, 'to' => "+91".$request->phone]);
            if ($verification->valid) {
                $user = User::where('phone', $request->phone)->first();
                $user->password = Hash::make($request->otp);
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
    
        return response()->json(['status' => true , 'data' => getProfile(auth()->user()->id), 'access_token' => $accessToken, 'msg' => 'login successfully'], 200);
    }
    
    public function sendOTP(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'phone' => 'required|min:10|max:10|exists:users,phone',
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $res['msg']]);
        }
        $loginData = $validator->validated();
        $phone = $loginData['phone'];
        $user = User::where('phone', $phone)->first();
        if($user)
        {
            $twr = $this->sendSMS($phone);
            if($twr == true)
            {
                return response()->json(['status'=>true,'msg' => "OTP sent successfully"]);
            }else
            {
                return response()->json(['status'=>false, 'msg' => $twr]);
            } 
        }else
        {
            return response()->json(['status'=>false, 'msg' => "Phone is not registered"]);
        }
        
    }
    public function sendOTPToNewNumber(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'phone' => 'required|min:10|max:10',
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $res['msg']], 200);
        }
        $loginData = $validator->validated();
        $phone = $loginData['phone'];
        $twr = $this->sendSMS($phone);
        if($twr == true) {
            return response()->json(['status'=>true,'msg' => "OTP sent successfully"], 200);
        }else {
            return response()->json(['status'=>false, 'msg' => $twr], 200);
        }   
    }
    public function sendSMS($receiverNumber)
    {
        $token = getenv("TWILIO_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $phone = "+91".$receiverNumber;
        try{
            $twilio = new Client($twilio_sid, $token);
            $verify = $twilio->verify->v2->services($twilio_verify_sid)
            ->verifications
            ->create($phone, 'sms');
            return true;
        }
        catch(\Exception $e ){
            return response()->json($e->getMessage());
        }
    }
    function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $re_message = 'Successfully logged out';
        return response()->json(['status' => true ,'message' => $re_message]);
    }
   
}
