<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\MasonDealer;
use App\Models\Reward;
use App\Models\RewardHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\State;
use App\Models\Setting;
use App\Models\Branch;
use App\Traits\HelperTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StarLinkNotification;
use Illuminate\Support\Facades\Http;
use App\Services\GoogleTranslateService;
use App\Utils\LocalLanguageTranslation;
class MasonRegistrationController extends Controller
{
    use HelperTrait;

    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }

    function register(Request $request)
    {
        try
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
                        'name' => 'required',
                        'phone' => 'required|min:10|max:10|unique:users,phone',
                        'branch_id'  => 'required',
                        'dealer_ids'   => 'required',
                        'address1' => 'required',
                        'address2' => 'required|max:500',
                        'city' => 'required',
                        'district' => 'required',
                        'state' => 'required',
                        'country' => 'required',
                        'pincode' => 'required',        
                        'dob' => 'required',
                        'aadhaar_no' => "nullable|size:12|unique:users,aadhaar_no|regex:/^[0-9]{12}$/",
                        'aadhaar_doc' => "nullable|image",
                        'voter_number' => "nullable|size:10|unique:users,voter_number|regex:/^[A-Za-z0-9]{10}$/",
                        'voter_doc' => "nullable|image",
                        'marital_status' => "required",
                        'phone_verified_at' => "required",
                    );
                    
            $messages = array(
                        "phone_verified_at.required" => "Please verify phone first",
                        "phone.min" => "invaild phone",
                        "phone.max" => "invailid phone",
                        "phone.unique" => "Mason is already registered.",
                        "address2.required" => "Address 2 is required.",
                        );
            $attributes = array(
                'name' => 'mason name',
                'branch_id' => 'branch',
                'dealer_ids' => 'dealer',
                'aadhaar_no'=> 'aadhaar',
                'voter_doc'=> 'voter ducument',
                'marital_status' => 'marital status',
                'phone' => 'mason phone',
                'phone_verified_at' => 'phone verified at'
            );

            // validation 
            $validator  = Validator::make($input,$rules,$messages,$attributes);
            $res = validationFailer($validator);
            if ($res['status'] == false) {
                return response()->json(['status' => false,'msg' => $this->googleTranslate->translateText($res['msg'], $targetLanguage)]);
            }
            $validatedData                = $validator->validated();
            $validatedData['parent']      = $request->user()->id;
            $validatedData['created_by']  = $request->user()->id;
            $validatedData['role']        = 2;
            $validatedData['spouse_name'] = $request->spouse_name;
            $validatedData['spouse_dob']  = $request->spouse_dob;
            $settingName                  = Setting::where('setting_name','registration_point')->first();
            $validatedData['registration_point'] = $settingName->setting_value;
            try{
                DB::beginTransaction();
                // $userRecord = User::where("phone", $request->phone)->first();
                // if(!empty($userRecord))
                // {
                //     return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("Phone_number_already_exists", $targetLanguage)]);
                // }
                // $userRecord = User::where("aadhaar_no", $request->aadhaar_no)->first();
                // if(!empty($userRecord))
                // {
                //     return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("Aadhaar_number_already_exists", $targetLanguage)]);
                // }
                $branchOb = Branch::find($request->branch_id);
                if(empty($branchOb))
                {
                    return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("branch_not_found", $targetLanguage)]);
                }
                if($branchOb->state?->is_voter_require == State::VOTER_REQUIRE_YES)
                {
                    if( (!$request->has("aadhaar_no") || empty($request->aadhaar_no)) && (!$request->has("voter_number") || empty($request->voter_number)) )
                    {
                        return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("aadhaar_or_voter_is_required_for_meghalaya_region", $targetLanguage)]);
                    }
                    if($request->has("voter_number") && !empty($request->voter_number))
                    {
                        if(!$request->has("voter_doc") || !$request->file('voter_doc'))
                        {
                            return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("voter_doc_is_required_if_voter_number_provided", $targetLanguage)]);
                        }
                    }
                    elseif($request->has("voter_doc") && !empty($request->voter_doc))
                    {
                        if(!$request->has("voter_number") || !$request->file('voter_number'))
                        {
                            return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("voter_number_is_required_if_voter_doc_provided", $targetLanguage)]);
                        }
                    }

                    if($request->has("aadhaar_no") && !empty($request->aadhaar_no))
                    {
                        if(!$request->has("aadhaar_doc") || !$request->file('aadhaar_doc'))
                        {
                            return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("aadhaar_doc_is_required_if_aadhaar_number_provided", $targetLanguage)]);
                        }
                    }
                    elseif($request->has("aadhaar_doc") && !empty($request->aadhaar_doc))
                    {
                        if(!$request->has("aadhaar_no") || !$request->file('aadhaar_no'))
                        {
                            return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("aadhaar_number_is_required_if_aadhaar_doc_provided", $targetLanguage)]);
                        }
                    }
                }
                elseif(!$request->has("aadhaar_no") || empty($request->aadhaar_no))
                {
                    return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("aadhaar_is_required", $targetLanguage)]);
                }
                elseif(!$request->has("aadhaar_doc") || !$request->file('aadhaar_doc'))
                {
                    return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate("aadhaar_doc_is_required", $targetLanguage)]);
                }
                if($branchOb->state?->is_voter_require != State::VOTER_REQUIRE_YES)
                {
                    $validatedData['voter_doc'] = null;
                    $validatedData['voter_number'] = null;
                }
                $user = User::create($validatedData);
                if(isset($user->id)){
                    if($request->file('aadhaar_doc')) {
                        $file = $request->file('aadhaar_doc');
                        $filename = "M".$user->id.".".$request->file('aadhaar_doc')->getClientOriginalExtension();
                        $location = base_path().'/public/aadhaar';
                        $file->move($location,$filename);
                        $user->aadhaar_doc = $filename;

                    }
                    if($request->file('voter_doc') && $branchOb->state?->is_voter_require == State::VOTER_REQUIRE_YES) {
                        $file = $request->file('voter_doc');
                        $filename = "V".$user->id.".".$request->file('voter_doc')->getClientOriginalExtension();
                        $location = base_path().'/public/voter';
                        $file->move($location,$filename);
                        $user->voter_doc = "voter/".$filename;
                        $user->save();

                    }
                    $user->save();
                    // for add Registration Bonus points
                    Reward::create([
                        'user_id'  => $user->id, 
                        'bag'         => 0, 
                        'description'         => 'Registration bonus points', 
                        'point'       =>  $this->getRegPoint(),
                        'is_verified' => 1 ,
                        'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_YES,
                        'is_bonus' => 1
                    ]) ;
                    $dealers =  json_decode($request->dealer_ids);
                    if(count($dealers) == 0)
                    {
                        DB::rollBack();
                        return response()->json(['status'=> false, 'msg' => $this->localLanguageTranslate->translate("Dealer/RSSD_not_selected", $targetLanguage)]);
                    }
                    $m_d = array();
                    foreach($dealers as $dealer){
                        $m_d[] = array('mason_id' => $user->id, 'dealer_id' => $dealer);
                    } 
                    MasonDealer::insert($m_d);
                    // DB::commit(); 
                    $this->updatePoint($user->id);
                    //Send Notification to Mason and Dealer
                    $masonMsg = "Registration Successfull.";
                    $masonNotificationData = [
                        "notification_type" => "Mason Registration",
                        "data" => [
                            "msg" => $masonMsg,
                        ]
                    ];
                    Notification::send($user, new StarLinkNotification($masonNotificationData));
                    foreach($dealers as $dealer)
                    {
                        // $dealerObject = User::find($dealer->id);
                        $dealerObject = User::find($dealer);
                        $dealerMsg = "New Mason Registered with you. Mason Name ".$user->name." Mason Phone ".$user->phone;
                        $dealerNotificationData = [
                            "notification_type" => "Mason Registration",
                            "data" => [
                                "msg" => $dealerMsg,
                            ]
                        ];
                        Notification::send($dealerObject, new StarLinkNotification($dealerNotificationData));
                    }
                    //Send SMS to Mason
                    $masonSMS = "Congratulation ! You have been successfully registered on Star Link - STAR CEMENT";
                    Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$user->phone.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
                    DB::commit(); 
                    return response()->json(['status'=> true, 'data' => $user, 'msg' => $this->localLanguageTranslate->translate("Mason_Registered_successfully", $targetLanguage)]);
                }
            
            }catch(Exception $e){
                DB::rollBack();
                return response()->json(['status'=> false, 'msg' => $this->googleTranslate->translateText($e->getMessage(), $targetLanguage)]);
            }
        }
        catch(Exception $e){
            DB::rollBack();
            return response()->json(['status'=> false, 'msg' => $this->googleTranslate->translateText($e->getMessage(), $targetLanguage)]);
        }
    }
    
    function verifyPhone(Request $request)
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
            'phone' => 'required|min:10',
            'otp' => 'required'
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $this->googleTranslate->translateText($res['msg'], $targetLanguage)]);
        }
        $phone_verified_at = verifyPhoneNumber($request->phone, $request->otp);
        if($phone_verified_at){
            return response()->json(['status'=> true, 'msg' => $this->localLanguageTranslate->translate("phone_verified_successfully", $targetLanguage),'data' => ['phone_verified_at' => $phone_verified_at]]);
        }else{
            return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate('Invalid_or_expired_OTP', $targetLanguage)]);
        }
        

    }
}

