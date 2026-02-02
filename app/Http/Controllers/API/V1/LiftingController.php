<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\CustomerLifting;
use App\Models\Lifting;
use App\Models\LiftingApprovalHistory;
use App\Models\MasonLifting;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\Product;
use App\Models\Log;
use App\Traits\HelperTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\ASMLiftingAction;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StarLinkNotification;
use App\Services\GoogleTranslateService;
use Illuminate\Support\Facades\Http;
use App\Utils\LocalLanguageTranslation;
class LiftingController extends Controller
{
    use HelperTrait;

    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }

    function checkDuplicateLifting(Request $request)
    {
        try {
            
            $targetLanguage = null;
            if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
            {
                $targetLanguage = $request->preferred_app_lang;
            }
            if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
            {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $message = "";
            if(\Auth::user()->role == User::MASON)
            {
                $lastLifting = Reward::with(["lifting"])->where("user_id", \Auth::user()->id)->latest("id")->first();
                if(!empty($lastLifting) && $lastLifting->created_at->isToday() && $lastLifting->bag == $request->qty && !empty($lastLifting->lifting) && $lastLifting->lifting->product_id == $request->product_id)
                {
                    if(!empty($lastLifting->lifting->req_by))
                    {
                        $message = $this->localLanguageTranslate->translate("Last_lifting_by_BDO_has_lifted_same_quantity_of_this_product._Do_you_want_to_proceed_?", $targetLanguage);
                    }
                    else
                    {
                        $message = $this->localLanguageTranslate->translate("Your_Last_lifting_has_same_quantity_of_this_product._Do_you_want_to_proceed_?", $targetLanguage);
                    }
                    return response()->json(['status' => false, 'msg' => $message]);
                }
                return response()->json(['status' => true, 'msg' => $message]);
            }
            else if(\Auth::user()->role == User::TECHNICAL_ENGINEER)
            {
                $masonIds = json_decode($request->mason_ids) ?? [];
                if(count($masonIds) == 0)
                {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Contractor_required.", $targetLanguage)]);
                }
                foreach($masonIds as $masonId)
                {
                    $masonRecord = User::where(["id" => $masonId, "status" => 1, "role" => User::MASON])->first();
                    if(empty($masonRecord))
                    {
                        return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Mason_not_found", $targetLanguage)]);
                    }
                    $lastLifting = Reward::with(["lifting"])->where("user_id", $masonId)->latest("id")->first();
                    if(!empty($lastLifting) && $lastLifting->created_at->isToday() && $lastLifting->bag == $request->qty && !empty($lastLifting->lifting) && $lastLifting->lifting->product_id == $request->product_id)
                    {
                        if(empty($lastLifting->lifting->req_by))
                        {
                            $message = $this->localLanguageTranslate->translate("Last_lifting_by_contractor_has_lifted_same_quantity_of_this_product._Do_you_want_to_proceed_?", $targetLanguage);
                        }
                        else
                        {
                            $message = $this->localLanguageTranslate->translate("Your_Last_lifting_has_same_quantity_of_this_product._Do_you_want_to_proceed_?", $targetLanguage);
                        }
                        return response()->json(['status' => false, 'msg' => $message]);
                    }
                }
                return response()->json(['status' => true, 'msg' => $message]);
            }
            else
            {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("You_does_not_have_this_permission.", $targetLanguage)]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        } 
    }

    function addLifting(Request $request)
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
        $logData = [
            'user_id' => Auth::user()->id,
            'request' => json_encode($request->all()),
            'action' => 'Add Lifting',
            'model_name' => 'Lifting',
        ];
        $logTable = Log::create($logData);
        //main method which is used for Prod.
        try {
           DB::beginTransaction();
            $input = $request->all();
            $rules = array(
                        'product_id' => 'required',
                        'mason_ids' => 'required',
                        'qty' => 'required',
                        'lifting_date' => 'required',
                        'dealer_rssd_id' => 'required',
                    );
            // if($this->settingVal('setting_name', 'lifting_by_otp_button') == 1)
            // {
            //     $req_type = 1;
            // }
            // else
            // {
            //     $req_type = 2;
            // }
            $req_type = 2;
            // if($request->has('req_type'))
            // {
            //     $req_type = $request->req_type;
            // }
            if($request->hasFile('img'))
            {
                $rules = array_merge($rules,['img' => 'mimes:jpeg,jpg,png|required']);
            }

            // if($request->user()->role == 1){
            //     $rules = array_merge($rules,['dealer_rssd_id' => 'required']);
            //     $user_id = $request->dealer_rssd_id;
            // }else{
            //     $user_id = $request->user()->id;
            // }
            $user_id = $request->user()->id;
            $dealer_id = $request->dealer_rssd_id;
        
            $validator  = Validator::make($input, $rules);
            $validRes = validateInput($validator);
            if ($validRes['status'] == false) {
                return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($validRes['msg'], $targetLanguage)]);
            }
            // Parse the date with Carbon
            $parsedDate = Carbon::createFromFormat('d-m-Y', $request->lifting_date)->startOfDay();

            // Get today's date and the date 5 days ago
            $today = Carbon::today();
            $numberPreviousDays = $this->settingVal('setting_name', 'app_lifting_date');
            $previousDate = $today;
            if($numberPreviousDays > 0 ?? false)
            {
                $previousDate = Carbon::today()->subDays($numberPreviousDays);
            }

            // Check if the date is valid
            if ($parsedDate->greaterThan($today)) {
                return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText('The lifting date cannot be in the future.', $targetLanguage)]);
            }

            if ($parsedDate->lessThan($previousDate)) {
                if($previousDate == $today)
                {
                    return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText('The lifting date cannot be in past.', $targetLanguage)]);
                }
                return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText('The lifting date cannot be more than '.$numberPreviousDays.' days ago.', $targetLanguage)]);
            }
            if(!$parsedDate->greaterThanOrEqualTo(Carbon::parse(Auth::user()->created_at)->startOfDay()))
            {
                return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText('Lifting date cannot be previous than your registration date.', $targetLanguage)]);
            }
            $data =  $validRes['validated_data'];
            if(\Auth::user()->role == 1)
            {
                foreach(json_decode($request->mason_ids) as $val)
                {
                    $checkMasonParent = User::find($val)->parent;
                    if($checkMasonParent != \Auth::user()->id)
                    {
                        return response()->json([
                            'status' => false, 
                            'msg' => $this->localLanguageTranslate->translate('Wrong_Mason', $targetLanguage)
                        ]);
                    }
                }
                $req_type = 2;
                $data['req_by'] = \Auth::user()->id;
            }
            $data['user_id'] = $dealer_id;
            $dbTargetLanguage = "en";
            // $data['remark'] = $this->googleTranslate->translateText($request->remark, $dbTargetLanguage);
            $data['remark'] = $request->remark;
            $data['req_type'] = $req_type;
            if($req_type != 1)
            {
                $data['req_status'] =  0;
                $data['seek_approval'] = 1;
                $data['seek_approval_from'] =  Carbon::now();
            }
            if($request->qty > $this->settingVal('setting_name', 'normal_lifting_limit'))
            {
                $data['req_status'] =  0;
                $data['seek_approval'] = 4;
                $data['seek_approval_from'] =  Carbon::now();
            }

            // return date('Y-m-d');
            //    return $this->availStock($request->product_id, $dealer_id);
            // return  $this->getCurrentMonthLifting($request->product_id, $dealer_id, $request->lifting_date);
            // return Carbon::now()->startOfMonth()->format('Y-m-d') ;
            $lifting = Lifting::create($data);
            $masons =  json_decode($request->mason_ids);
            $products=Product::where('id', $request->product_id)->first();
            if($req_type != 1 || $request->qty > $this->settingVal('setting_name', 'normal_lifting_limit'))
            {
                $bonusPoint = 0;
                $masonBranchId = User::find($masons[0])->branch_id;
                if($lifting->qty >  $products->more_than_bags)
                {
                    $bonusPoint = $products->bonus_points;
                }
                $liftingApprovalHistory = [
                    'lifting_id' => $lifting->id,
                    'qty' => $lifting->qty,
                    'point' => $this->getPoint($lifting->product_id, $lifting->qty),
                    'bonus_point' => $bonusPoint,
                    'seek_approval' => 1,
                    'seek_approval_by' => $dealer_id,
                    'seek_approval_from' => Carbon::now(),
                    'approval_window' => $this->settingVal('setting_name', 'dealer/rssd_approval_window'),
                    'action_status' => 0,
                    'action_taken_by' => \Auth::user()->id,
                ];
                if($request->qty > $this->settingVal('setting_name', 'normal_lifting_limit'))
                {
                    $asmID = $this->getASMIdByBranch($masonBranchId);
                    if(empty($asmID))
                    {
                        return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('ASM_not_available_please_contact_admin', $targetLanguage)]);
                    }
                    $liftingApprovalHistory['seek_approval'] = 4;
                    $liftingApprovalHistory['seek_approval_by'] = $asmID;
                    $liftingApprovalHistory['approval_window'] = $this->settingVal('setting_name', 'asm_approval_window');
                }
                LiftingApprovalHistory::create($liftingApprovalHistory);
            }
            $date = date('d/m/Y');
            $qtys = $lifting->qty;
            // $products=Product::where('id', $request->product_id)->first();
            $pname=$products->name;
            $desc_text = " lifting of $qtys $pname bags on date $date for earned ";
            // for add reward points
           // $mason =  $request->mason_ids;
        if($req_type == 1 && !($request->qty > $this->settingVal('setting_name', 'normal_lifting_limit')))
        {
            $isVerified =1;
        }
        else
        {
            $isVerified =null;
        }
        $m_id = 0;
        $checkQty=0;
        $res90 =0;

        $masonLiftingTable=[];
        $rewardTable = [];
         foreach($masons as $mason){
         $m_id =$mason;
         // check for restrictions
        //    $resavg =  $this->getLiftingAvg($request->product_id, $dealer_id);
           
        //    if($resavg <= $lifting->qty )
        //     {
        //          $isVerified =0;
        //     }
        if($req_type == 1 && !($request->qty > $this->settingVal('setting_name', 'normal_lifting_limit')))
        {
            $dealerAvailableStock =  $this->availStock($request->product_id, $dealer_id, $request->lifting_date);
                    
            
            //$checkQty =  $this->getLiftingCurrMonthMason($request->product_id, $dealer_id, $m_id)+$lifting->qty ;
            $currentMonthLiftings =  $this->getCurrentMonthLifting($request->product_id, $dealer_id, $request->lifting_date) ;
            //storing availStock
            $lifting->available_stock = $dealerAvailableStock - $currentMonthLiftings;
            $lifting->save();
            if(($dealerAvailableStock - $currentMonthLiftings) < $lifting->qty)
            {
                    $isVerified =0;
            }
        }
          
            // $m_l = array();
            
                $m_l = array('lifting_id' => $lifting->id, 'mason_id' => $mason);
				

				 // for add reward points
                 $rewardTable[] = Reward::create([
                'lifting_id'  => $lifting->id, 
                'user_id'  => $mason, 
                'bag'         => $lifting->qty, 
              'description'         => $desc_text,
                'point'       =>  $this->getPoint($lifting->product_id, $lifting->qty),
                'is_verified' => $isVerified ,
                'is_eligible_for_ledger' => $isVerified == Reward::VERIFIED ? RewardHistory::ELIGIBLE_FOR_LEDGER_YES : RewardHistory::ELIGIBLE_FOR_LEDGER_NO,
                ]) ;

                // add bonus points after lifting points add 

                if($lifting->qty >  $products->more_than_bags)
                {
                    $total_bonus_points=$products->bonus_points; 
                     // for add reward points
                     $rewardTable[] = Reward::create([
                    'lifting_id'  => $lifting->id, 
                     'user_id'  => $mason, 
                     'bag'         => $lifting->qty, 
                     'description'         => 'Bonus points added of '.$lifting->qty." $pname bags lifting",
                     'point'       =>  $total_bonus_points,
                      'is_verified' => $isVerified  ,
                      'is_eligible_for_ledger' => $isVerified == Reward::VERIFIED ? RewardHistory::ELIGIBLE_FOR_LEDGER_YES : RewardHistory::ELIGIBLE_FOR_LEDGER_NO,
                      'is_bonus' => 1  ,
                  ]) ;

                }  
                $masonLiftingTable[] = MasonLifting::create($m_l);              
            }
            if($request->file('img')) {
                $file = $request->file('img');
                $filename = "L".$lifting->id.".".$request->file('img')->getClientOriginalExtension();
                $location = base_path().'/public/lifting';
                $file->move($location,$filename);
                $lifting->img = asset('/public/lifting').'/'.$filename;
                $lifting->save();
            }
            //for update the pointsof mason 
            if($req_type == 1)//will added
            {
                $this->updatePoint($mason);
            }
            $mason = User::find($masons[0]);
            $te = User::find($mason->parent);
            $dealer = User::find($lifting->user_id);
            if($request->qty > $this->settingVal('setting_name', 'normal_lifting_limit'))
            {
                $emailSendTo = [];
                $ASMEmailId = User::find($this->getASMIdByBranch($mason->branch_id))->email ?? null;
                if($ASMEmailId != null)
                {
                    array_push($emailSendTo, $ASMEmailId);
                }
                $operationalEmails = explode(',', $this->settingVal('setting_name', 'operational_emails'));
                foreach($operationalEmails as $operationalEmail)
                {
                    if($operationalEmail != null)
                    {
                        array_push($emailSendTo, $operationalEmail);
                    }
                }
                // if(\Auth::user()->role == 1)
                // {
                //     $te = \Auth::user();
                // }
                foreach($emailSendTo as $emailId)
                {
                    Mail::to($emailId)->send(new ASMLiftingAction([
                        'mason_name' => $mason->name ?? null,
                        'mason_mobile' => $mason->phone ?? null,
                        'te_name' => $te->name ?? null,
                        'te_code' => $te->emp_code ?? null,
                        'dealer_rssd_name' => $dealer->name ?? null,
                        'dealer_rssd_code' => $dealer->emp_code ?? null,
                        'product_name' => $products->name,
                        'qty' => $lifting->qty,
                        'lifting_date' => $lifting->created_at,
                        'remarks' => $lifting->remark,
                        'approve_link' => url('').'/web/public/asm/lifting/action/'.encrypt($lifting->id).'/'.encrypt(1),
                        'reject_link' => url('').'/web/public/asm/lifting/action/'.encrypt($lifting->id).'/'.encrypt(0),
                    ]));
                }
            }
            if($lifting->req_by == null)
            {
                $msg = "Lifting of ".$lifting->qty." ".$products->name." bags by Mason ".($mason->name ?? null)." Phone No. ".($mason->phone ?? null);
            }
            else
            {
                $msg = "Lifting of ".$lifting->qty." ".$products->name." bags by BD ".($te->name ?? null)." behalf of Mason ".($mason->name ?? null)." Phone No. ".($mason->phone ?? null);
            }
            //Send Notification to Mason and Dealer
            $notificationData = [
                "notification_type" => "Lifting",
                "data" => [
                    "msg" => $msg,
                ]
            ];
            Notification::send($mason, new StarLinkNotification($notificationData));
            Notification::send($dealer, new StarLinkNotification($notificationData));
            // Send SMS to Mason
            $receiverNumber = $mason->phone;
            $masonSMS = "New lifting: ".$lifting->qty." ".$products->name." bags (".$receiverNumber.") Successfully Registered. - Star Link";
            // Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$receiverNumber.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
            DB::commit();
            $tables = json_encode([
                'Lifting' => $lifting,
                'Reward' => $rewardTable,
                'MasonLifting' => $masonLiftingTable,
            ]);
            $logTable->update([
                'response' => $tables
            ]);
            if($req_type == 1 && !($request->qty > $this->settingVal('setting_name', 'normal_lifting_limit')))
            {
                if(($dealerAvailableStock - $currentMonthLiftings)< $lifting->qty)
                {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Lifting_is_rejected,_please_contact_admin', $targetLanguage)]);
            
                }else
                {
                    return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('lifting_added_successfully', $targetLanguage)]);  
                }
            }
            else
            {
                if($req_type == 2 && !($request->qty > $this->settingVal('setting_name', 'normal_lifting_limit')))
                {
                    return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('lifting_request_sent_to_Dealer', $targetLanguage)]);
                }
                else
                {
                    return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('lifting_request_sent_to_ASM', $targetLanguage)]);
                }
            }
        
        } catch (Exception $e) {
           DB::rollBack();
           $logTable->update([
                'response' => $e->getMessage()
            ]);
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        } 
    }

    function addLiftingByStarSathi(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'lifting_id' => 'required'
        );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $validRes['msg']]);
        }
        $data =  $validRes['validated_data'];
        $lifting = Lifting::find($data['lifting_id']);
        if($lifting === null) {
            return response()->json(['status' => false, 'msg' => 'Invalid Lifting.']);
        }
        //keep log
        $logData = [
            'user_id' => $lifting->user_id,
            'request' => json_encode($request->all()),
            'action' => 'Add Lifting By Star Sathi App',
            'model_name' => 'Lifting',
        ];
        $logTable = Log::create($logData);
        //keep log
        try {
            DB::beginTransaction();
            $dealerAvailableStock =  $this->availStock($lifting->product_id, $lifting->user_id, $lifting->lifting_date);
            $currentMonthLiftings =  $this->getCurrentMonthLifting($lifting->product_id, $lifting->user_id, $lifting->lifting_date) ;
            
            $lifting->available_stock = $dealerAvailableStock - $currentMonthLiftings;
            $lifting->req_status = 1;
            $lifting->save();
            $isVerified = 1;
            if(($dealerAvailableStock - $currentMonthLiftings) < $lifting->qty)
            {
                $isVerified = 0;
            }
            $rewards = Reward::where('lifting_id', $lifting->id)->get();
            $masonId = $rewards[0]->user_id;
            foreach($rewards as $reward)
            {
                $reward->is_verified = $isVerified;
                $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                if($isVerified == Reward::VERIFIED)
                {
                    $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                }
                $reward->is_eligible_for_ledger = $isEligibleForLedgerInRewardTable;
                $reward->save();
            }
            $this->updatePoint($masonId);
            DB::commit();
            $tables = json_encode([
                'Lifting' => $lifting,
                'Reward' => $rewards,
            ]);
            $logTable->update([
                'response' => $tables
            ]);
            if(($dealerAvailableStock - $currentMonthLiftings)< $lifting->qty)
            {
                return response()->json(['status' => false, 'msg' => 'Lifting is rejected, please contact admin']);
                    
            }else
            {
                return response()->json(['status' => true, 'msg' => 'lifting added successfully']);  
            }
        } catch (Exception $e) {
            DB::rollBack();
            $logTable->update([
                'response' => $e->getMessage()
            ]);
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }

    }

    public function pendingLiftingsByStarSathi(Request $request)
    {
        $dealer = User::where('sap_code',$request->sap_code)->first();
        if(!$dealer)
        {
            return response()->json(['status' => false, 'msg' => 'Invalid SAP Code.']);
        }
        if(!$request->has('from_date') && !$request->has('to_date'))
        {
            $pendingLiftings = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 0
            ])->with(['product', 'reward'])->get();
        }
        else
        {
            $from_date = date("Y-m-d", strtotime("31-12-1990"));
            $to_date = date('Y-m-d');
            if($request->has('from_date'))
            {
                $from_date = date("Y-m-d", strtotime($request->from_date));
            }
            if($request->has('to_date'))
            {
                $to_date = date("Y-m-d", strtotime($request->to_date));
            }
            $pendingLiftings = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 0
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'")
            ->get();
        }
        $pendingLists = [];
        foreach($pendingLiftings as $pendingLifting)
        {
            $pendingList = [
                "lifting_id" => $pendingLifting->id,
                "products" => $pendingLifting->product->name ?? "",
                "no_of_bags" => $pendingLifting->qty ?? "",
                "lifting_date" => $pendingLifting->lifting_date ?? "",
                "mason_name" => $pendingLifting->reward->mason->name ?? "",
                "mason_phone" => $pendingLifting->reward->mason->phone ?? ""
            ];
            $pendingLists[] = $pendingList;
        }
        return response()->json(['status' => true, 'msg' => 'Lifting Pending Lists Get Successfull.', 'data' => $pendingLists]);
    }

    public function rejectedLiftingsByStarSathi(Request $request)
    {
        // if($request->has('from_date'))
        $dealer = User::where('sap_code',$request->sap_code)->first();
        if(!$dealer)
        {
            return response()->json(['status' => false, 'msg' => 'Invalid SAP Code.']);
        }
        if(!$request->has('from_date') && !$request->has('to_date'))
        {
            $pendingLiftings = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 2
            ])->with(['product', 'reward'])->get();
        }
        else
        {
            $from_date = date("Y-m-d", strtotime("31-12-1990"));
            $to_date = date('Y-m-d');
            if($request->has('from_date'))
            {
                $from_date = date("Y-m-d", strtotime($request->from_date));
            }
            if($request->has('to_date'))
            {
                $to_date = date("Y-m-d", strtotime($request->to_date));
            }
            $pendingLiftings = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 2
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'")
            ->get();
        }
        $pendingLists = [];
        foreach($pendingLiftings as $pendingLifting)
        {
            $pendingList = [
                "products" => $pendingLifting->product->name ?? "",
                "no_of_bags" => $pendingLifting->qty ?? "",
                "lifting_date" => $pendingLifting->lifting_date ?? "",
                "points" => "In-progress",
                "mason_name" => $pendingLifting->reward->mason->name ?? "",
                "mason_phone" => $pendingLifting->reward->mason->phone ?? ""
            ];
            $pendingLists[] = $pendingList;
        }
        return response()->json(['status' => true, 'msg' => 'Lifting Rejection Lists Get Successfull.', 'data' => $pendingLists]);
    }

    public function approvedLiftingsByStarSathi(Request $request)
    {
        $dealer = User::where('sap_code',$request->sap_code)->first();
        if(!$dealer)
        {
            return response()->json(['status' => false, 'msg' => 'Invalid SAP Code.']);
        }
        if(!$request->has('from_date') && !$request->has('to_date'))
        {
            $pendingLiftings = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 1
            ])->with(['product', 'reward'])->get();
        }
        else
        {
            $from_date = date("Y-m-d", strtotime("31-12-1990"));
            $to_date = date('Y-m-d');
            if($request->has('from_date'))
            {
                $from_date = date("Y-m-d", strtotime($request->from_date));
            }
            if($request->has('to_date'))
            {
                $to_date = date("Y-m-d", strtotime($request->to_date));
            }
            $pendingLiftings = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 1
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'")
            ->get();
        }
        $pendingLists = [];
        // return $pendingLiftings;
        foreach($pendingLiftings as $pendingLifting)
        {
            $points = 0;
            $rewards = Reward::where('lifting_id', $pendingLifting->id)->get();
            foreach($rewards as $reward)
            {
                $points+= $reward->point;
            }
            $pendingList = [
                "products" => $pendingLifting->product->name ?? "",
                "no_of_bags" => $pendingLifting->qty ?? "",
                "lifting_date" => $pendingLifting->lifting_date ?? "",
                "point" => $points,
                "mason_name" => $pendingLifting->reward->mason->name ?? "",
                "mason_phone" => $pendingLifting->reward->mason->phone ?? ""
            ];
            $pendingLists[] = $pendingList;
        }
        return response()->json(['status' => true, 'msg' => 'Lifting Accepted Lists Get Successfull.', 'date' => $pendingLists]);
    }

    public function rejectLiftingByStarSathi(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'lifting_id' => 'required'
        );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $validRes['msg']]);
        }
        $data =  $validRes['validated_data'];
        $lifting = Lifting::find($data['lifting_id']);
        if($lifting)
        {
            $lifting->req_status = 2;
            $lifting->save();
            return response()->json(['status' => true, 'msg' => 'Lifting Rejected.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Lifting Id.']);
        
    }

    // function liftingAdd(Request $request)
    // {
    //    // return $request->product_id;
    //     try {
    //         DB::beginTransaction();
    //         $input = $request->all();
    //         $rules = array(
    //                     'product_id' => 'required',
    //                     'mason_ids' => 'required',
    //                     'qty' => 'required',
    //                     'lifting_date' => 'required',
    //                 );
    //         if($request->hasFile('img'))
    //         {
    //             $rules = array_merge($rules,['img' => 'mimes:jpeg,jpg,png|required']);
    //         }

    //         // if($request->user()->role == 1){
    //         //     $rules = array_merge($rules,['dealer_rssd_id' => 'required']);
    //         //     $user_id = $request->dealer_rssd_id;
    //         // }else{
    //         //     $user_id = $request->user()->id;
    //         // }
    //         $user_id = 70;
    //         $dealer_id = $request->dealer_rssd_id;
        
    //         $validator  = Validator::make($input, $rules);
    //         $validRes = validateInput($validator);
    //         if ($validRes['status'] == false) {
    //             return response()->json(['status' => false, 'msg' => $validRes['msg']]);
    //         }
    //         $data =  $validRes['validated_data'];
    //         $data['user_id'] = $dealer_id;
    //         $data['remark'] = $request->remark;
    //         $lifting = Lifting::create($data);
            
    //         $date = date('d/m/Y');
    //         $qtys = $lifting->qty;
    //         $products=Product::where('id', $request->product_id)->first();
    //         $pname=$products->name;
    //         $desc_text = " lifting of $qtys $pname bags on date $date for earned ";
    //         // for add reward points
    //         $masons =  json_decode($request->mason_ids);
    //        // $mason =  $request->mason_ids;
    //      $isVerified =1;
    //     $m_id = 0;
    //     $checkQty=0;
    //     $res90 =0;
    //      foreach($masons as $mason){
    //      $m_id =$mason;
    //      // check for restrictions
    //        $resavg =  $this->getLiftingAvg($request->product_id, $dealer_id);
       
    //        if($resavg <= $lifting->qty )
    //         {
    //              $isVerified =0;
    //         }
         
    //        $res90 =  $this->getLifting90($request->product_id, $dealer_id);
           
    //       $checkQty =  $this->getLiftingCurrMonthMason($request->product_id, $dealer_id, $m_id)+$lifting->qty ;
    //      // return "Lifting Average: ".$checkQty ;
    //      if($res90 <= $checkQty )
    //         {
    //             $isVerified =0;
    //           // return response()->json(['status'=> false, 'msg' => "you can not add more than  90%  of the avg data of the previous month", 'data' => []]);
    //         }
    //      // check for restrictions
         
          
    //         $m_l = array();
            
    //             $m_l[] = array('lifting_id' => $lifting->id, 'mason_id' => $mason);
				

	// 			 // for add reward points
    //         Reward::create([
    //             'lifting_id'  => $lifting->id, 
    //             'user_id'  => $mason, 
    //             'bag'         => $lifting->qty, 
    //           'description'         => $desc_text,
    //             'point'       =>  $this->getPoint($lifting->product_id, $lifting->qty),
    //             'is_verified' => $isVerified ,
    // 'is_eligible_for_ledger' => $isVerified == Reward::VERIFIED ? RewardHistory::ELIGIBLE_FOR_LEDGER_YES : RewardHistory::ELIGIBLE_FOR_LEDGER_NO,
    //             ]) ;

    //             // add bonus points after lifting points add 

    //             if($lifting->qty >  $products->more_than_bags)
    //             {
    //                 $total_bonus_points=$products->bonus_points; 
    //                  // for add reward points
    //         Reward::create([
    //                 'lifting_id'  => $lifting->id, 
    //                  'user_id'  => $mason, 
    //                  'bag'         => $lifting->qty, 
    //                  'description'         => 'Bonus points added of '.$lifting->qty." $pname bags lifting",
    //                  'point'       =>  $total_bonus_points,
    //                   'is_verified' => $isVerified  ,
    // 'is_eligible_for_ledger' => $isVerified == Reward::VERIFIED ? RewardHistory::ELIGIBLE_FOR_LEDGER_YES : RewardHistory::ELIGIBLE_FOR_LEDGER_NO,
    //                   'is_bonus' => 1  ,
    //               ]) ;

    //             }                
    //         }
    //         MasonLifting::insert($m_l);
    //         if($request->file('img')) {
    //             $file = $request->file('img');
    //             $filename = "L".$lifting->id.".".$request->file('img')->getClientOriginalExtension();
    //             $location = base_path().'/public/lifting';
    //             $file->move($location,$filename);
    //             $lifting->img = asset('/public/lifting').'/'.$filename;
    //             $lifting->save();
    //         }
    //         //for update the pointsof mason 
    //         $this->updatePoint($mason);
    //        DB::commit();
             
    //      if($res90 <= $checkQty)
    //         {
    //            return response()->json(['status' => false, 'msg' => 'Lifting is rejected, please contact admin']);
         
    //         }else
    //          {
    //             return response()->json(['status' => true, 'msg' => 'lifting add successfully']);  
    //          }
        
    //     } catch (Exception $e) {
    //        DB::rollBack();
    //         return response()->json(['status' => false, 'msg' => $e->getMessage()]);
    //     } 
    // }
    function liftingHistory(Request $request) {
        $role = $request->user()->role; 
        $id   = $request->user()->id;
        $editable = false;
        // $editable = true;
        // $liftings = $this->liftingHistoryCoreQuery();
        // $liftings = $this->liftingHistoryCoreSelect($liftings);
        // $liftings = $liftings->get();
        if($role == 2)  {
            $editable = true;
            $liftings = $this->liftingHistoryCoreQuery();
             $liftings = $liftings->where('R.user_id',$request->user()->id);
            $liftings = $this->liftingHistoryCoreSelect($liftings);
            $liftings = $liftings->groupBy('R.lifting_id', 'U2.name','U2.phone','U2.aadhaar_no','U2.id', 'U.id', 'U.name', 'L.id','L.req_type','L.req_status','L.action_taken_at','L.qty','L.lifting_date','L.remark','P.id','P.name','R.lifting_id','R.is_verified');
            $liftings = $liftings->orderBy('R.id', 'DESC');
            
        }
    else  if($role == 1)  {
            $masonArr=User::where('parent', $id)->pluck('id')->toArray();
            $liftingIdArr=MasonLifting::whereIn('mason_id',$masonArr)->pluck('lifting_id')->toArray();
            $liftings = $this->liftingHistoryCoreQuery();
            $liftings = $liftings->whereIn('L.id',$liftingIdArr);
            $liftings = $this->liftingHistoryCoreSelect($liftings);
            //$liftings = $liftings->groupBy('ML.lifting_id');
            $liftings = $liftings->groupBy('R.lifting_id', 'U2.name','U2.phone','U2.aadhaar_no','U2.id', 'U.id', 'U.name', 'L.id','L.req_type','L.req_status','L.action_taken_at','L.qty','L.lifting_date','L.remark','P.id','P.name','R.lifting_id','R.is_verified');
            $liftings = $liftings->orderBy('R.id', 'DESC');
            $liftings = $liftings->orderBy('R.id', 'DESC');
        }
    else {
            $liftings = $this->liftingHistoryCoreQuery();
            $liftings = $liftings->where('L.user_id',$request->user()->id);
            $liftings = $this->liftingHistoryCoreSelect($liftings);
            $liftings = $liftings->groupBy('R.lifting_id', 'U2.name','U2.phone','U2.aadhaar_no','U2.id', 'U.id', 'U.name', 'L.id','L.req_type','L.req_status','L.action_taken_at','L.qty','L.lifting_date','L.remark','P.id','P.name','R.lifting_id','R.is_verified');
            $liftings = $liftings->orderBy('R.id', 'DESC');
             $liftings = $liftings->orderBy('R.id', 'DESC');
        }
        if(!empty(\Auth::user()->preferred_app_lang ?? null))
        {
            $page = 1;
            if($request->has("page") && $request->page != null)
            {
                $page = $request->page;
            }
            if($page < 1)
            {
                return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
            }
            $targetLanguage = (\Auth::user()->preferred_app_lang);
            $limit = 6;
            $fetchDataFrom = $limit * ($page - 1);
            $liftings = $liftings->skip($fetchDataFrom)->take($limit)->get();
            foreach($liftings as $key => $lifting)
            {
                $liftings[$key]->transaction_id = "LF".str_pad($lifting->lifting_id,10,"0",STR_PAD_LEFT );
            //     // $liftings[$key]->mason_name = $this->googleTranslate->translateText($lifting->mason_name, $targetLanguage);
            //     $liftings[$key]->mason_name = $lifting->mason_name;
            //     // $liftings[$key]->dealer_name = $this->googleTranslate->translateText($lifting->dealer_name, $targetLanguage);
            //     $liftings[$key]->dealer_name = $lifting->dealer_name;
            //     // $liftings[$key]->product_name = $this->googleTranslate->translateText($lifting->product_name, $targetLanguage);
            //     $liftings[$key]->product_name = $lifting->product_name;
            //     // $liftings[$key]->mason_category = $this->googleTranslate->translateText($lifting->mason_category, $targetLanguage);
            //     $liftings[$key]->mason_category = $lifting->mason_category;
            }
        }
        else
        {
            $liftings = $liftings->get();
        }
        if($liftings->isEmpty())  
        return response()->json(['status'=> false, 'msg' => "No Data", 'data' => []]);
        else
        return response()->json(['status'=> true, 'msg' => "History get successfully ", 'data' => $liftings, 'editable' => $editable]);
        
    }

    function liftingHistoryCoreQuery() {
        $historyUpto = $this->settingVal('setting_name', 'history_upto');
        $liftings = DB::table('mason_lifting as ML')
        ->LeftJoin('lifting as L','L.id','=','ML.lifting_id')
        ->LeftJoin('rewards as R','L.id','=','R.lifting_id')
        ->Join('products as P','P.id','=','L.product_id')
        ->LeftJoin('users as U','U.id','L.user_id')
        ->LeftJoin('users as U2','U2.id','ML.mason_id');
        if(!empty($historyUpto) && $historyUpto > 0)
        {
            // dd(Carbon::now()->subDays($historyUpto)->format("d-m-Y"));
            return $liftings->where(DB::raw("STR_TO_DATE(lifting_date, '%d-%m-%Y')"), '>=', Carbon::now()->subDays($historyUpto));
        }
        return $liftings;
    }
    function liftingHistoryCoreSelect($query){
        $query->select('L.id as lifting_id','L.req_type as request_send_to','L.req_status as star_sathi_status','L.action_taken_at','U2.name as mason_name','U2.phone as mason_phone','U2.aadhaar_no as mason_aadhaar_no','U2.id as mason_id', 'U.id as dealer_id', 'U.name as dealer_name','P.id as product_id','P.name as product_name','L.qty','L.lifting_date','L.remark', DB::raw('SUM(R.point) as point'), 'R.is_verified as is_verified','U.name as mason_category');
        return $query;
    } 
    


    function updateLifting(Request $request)
    {
            $input = $request->all();
            $rules = array(
                        'id' => 'required',
                        'product_id' => 'required',
                        'qty' => 'required',
                    );
            if($request->hasFile('img'))
            {
                $rules = array_merge($rules,['img' => 'mimes:jpeg,jpg,png|required']);
            }
            $validator  = Validator::make($input, $rules);
            $validRes = validateInput($validator);
            if ($validRes['status'] == false) {
                return response()->json(['status' => false, 'msg' => $validRes['msg']]);
            }
            $data =  $validRes['validated_data'];

            $rowsData = Reward::where('lifting_id',$request->id)->where('is_verified',0)->first();
            if($rowsData){
                
                $date = date('d/m/Y');
                $qtys = $request->qty;
                $desc_text = " lifting of $qtys bags on date $date for earned ";

                $data['product_id'] = $request->product_id;
                $data['qty'] = $request->qty;
                $data['remark'] = $request->remark;
                Lifting::where('id', $request->id)->update($data);
               // update the rewards of lifting of mason
                Reward::where('lifting_id', $request->id)->update([
                    'bag'         => $request->qty, 
                    'description'         => $desc_text, 
                    'point'       =>  $this->getPoint($request->product_id, $request->qty),
                    ]) ;

               return response()->json(['status' => true, 'msg' => "lifting updated successfully"]);

            }else
            {
                return response()->json(['status' => false, 'msg' => "not able to update cause of this lifting already verified by admin."]);
            }
    }    
    

    public static function getDealerIds()
    {
        return array_unique(Lifting::where('lifting_date','LIKE','__-08-____')->pluck('user_id')->toArray());
    }


    public function correctLiftings()
    {
        $dealerIds = LiftingController::getDealerIds();
        $product_id=2;
        $lifting_month = 8;
        $chgVerifiedToUnverified=[];
        $chgUnverifiedToVerified=[];
        $defectedDealers = [];
        $masonIds =[];
        foreach($dealerIds as $dealerId)
        {
            $dealerAvailableStock = LiftingController::availableStock($product_id, $dealerId, $lifting_month);
            $liftings = Lifting::where('user_id', $dealerId)->where('product_id', $product_id)->where('lifting_date', 'LIKE', '__-08-____')->get();
            foreach($liftings as $lifting)
            {
                $is_verified = 1;
                if($dealerAvailableStock < $lifting->qty)
                {
                    $is_verified = 0;
                }
                else
                {
                    $dealerAvailableStock -= $lifting->qty;
                }
                $litingVerifiedStatus = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->first();
                if($is_verified == 1 && $litingVerifiedStatus->is_verified != 1)
                {
                    $defectedDealers[] = $lifting->user_id;
                    $chgUnverifiedToVerified[] = $lifting->id;
                    $masonIds[] = $litingVerifiedStatus->user_id;
                }
                if($is_verified == 0 && $litingVerifiedStatus->is_verified != 0)
                {
                    $defectedDealers[] = $lifting->user_id;
                    $chgVerifiedToUnverified[] = $lifting->id;
                    $masonIds[] = $litingVerifiedStatus->user_id;
                }
            }
        }
        return response()->json([
            'status' => true,
            'msg' => 'Liftings need to be Corrected', 
            'Defected Dealer Ids' => array_unique($defectedDealers),
            'Defected Mason Ids' => array_unique($masonIds),
            'Unverified to Verified' => $chgUnverifiedToVerified,
            'Verified to Unverified' => $chgVerifiedToUnverified
        ]);
    }

    public function updateCorrectLiftings()
    {
        $chgVerifiedToUnverified = [
            13883,
            13885,
            13886,
            14050,
            13744,
            13748,
            13756,
            13813,
            14076,
            14056,
            13848,
            13783,
            13858,
            13878,
            14127
        ];
        foreach($chgVerifiedToUnverified as $val)
        {
            $rewardIds = Reward::where('lifting_id', $val)->pluck('id')->toArray();
            foreach($rewardIds as $rewardId)
            {
                $reward = Reward::find($rewardId);
                $reward->is_verified = 0;
                $reward->is_eligible_for_ledger = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                $reward->save();
            }
        }
        return response()->json([
            'status' => true,
            'msg' => 'Done'
        ]);
    }
    public function updateCorrectLiftingPoints()
    {
        $masonIds = [
            36728,
            39366,
            33463,
            33021,
            35772,
            33251,
            33248,
            33237,
            35119,
            35946,
            37110,
            37400,
            31339,
            36234,
            39378,
            39383,
            38812
        ];
        foreach($masonIds as $val)
        {
            $this->updatePoint($val);
        }
        return response()->json([
            'status' => true,
            'msg' => 'Done'
        ]); 
    }

    // public function correctLiftings()
    // {
    //     set_time_limit(0);
    //     $lifting_ids = Lifting::where('lifting_date','LIKE','__-07-____')->get();
    //     // $reward_ids = Reward::whereIn('lifting_id',$lifting_ids)->where(['is_bonus'=>0])->pluck('lifting_id')->toArray();
    //     $chgVerifiedToUnverified=[];
    //     $chgUnverifiedToVerified=[];
    //     $dealerIds = [];
    //     foreach($lifting_ids as $lifting_id)
    //     {
    //         $is_verified = 1;
    //         $dealerAvailableStock =  LiftingController::availStock($lifting_id->product_id, $lifting_id->user_id, $lifting_id->lifting_date);
    //         $currentMonthLiftings =  LiftingController::getCurrentMonthLifting($lifting_id->product_id, $lifting_id->user_id, $lifting_id->lifting_date, $lifting_id->id) ;
    //         $litingVerifiedStatus = Reward::where('lifting_id', $lifting_id->id)->first();
    //         if(($availStock = $dealerAvailableStock - $currentMonthLiftings) < $lifting_id->qty)
    //         {
    //             $is_verified = 0;
    //         }
    //         if($is_verified == 1 && $litingVerifiedStatus->is_verified != 1)
    //         {
    //             $dealerIds[] = $lifting_id->user_id;
    //             $chgUnverifiedToVerified[] = $lifting_id->id." Qty: ". $lifting_id->qty." Avail Stock: ".$availStock;
    //         }
    //         if($is_verified == 0 && $litingVerifiedStatus->is_verified != 0)
    //         {
    //             $dealerIds[] = $lifting_id->user_id;
    //             $chgVerifiedToUnverified[] = $lifting_id->id." Qty: ". $lifting_id->qty." Avail Stock: ".$availStock;
    //         }
    //     }
    //     return response()->json([
    //         'status' => true,
    //         'msg' => 'Liftings need to be Corrected', 
    //         'Dealer Ids' => array_unique($dealerIds),
    //         'Unverified to Verified' => $chgUnverifiedToVerified,
    //         'Verified to Unverified' => $chgVerifiedToUnverified
    //     ]);
    // }
    public static function availableStock($product_id='', $dealer_id='', $lifting_month = '')
    {
        // find the 3 month before month and years
        //  $curr = date("m-Y");

        // $curr = date("m-Y", strtotime("2023-07"));
        
        //     $month1 = date("m-Y",strtotime("-2 Months", strtotime($lifting_date)));
        //     $month2 = date("m-Y",strtotime("-3 Months", strtotime($lifting_date)));
        //     $arr1 = explode("-",$month1);
        //     $arr2 = explode("-",$month2);
        //     $years=array($arr1[1],$arr2[1]);
        // $marr1 = ltrim($arr1[0],'0');
        // $marr2 = ltrim($arr2[0],'0');    
        //     $months=array($marr1,$marr2);

            // $months=array("5","4");
        $years = [2023,2023];
        $months = [$lifting_month - 2, $lifting_month - 3];
    
    
            
            // find the 3 month before month and years
            $liftcount  = Lifting::where('user_id', $dealer_id)->count();
        //   $datas = CustomerLifting::whereIn('year', $years)->whereIn('month', $months)->where('dealer_id', $dealer_id)->where('product_id', $product_id)->sum('quantity');  
        $datas1 = CustomerLifting::where('year', $years[0])->where('month', $months[0])
        ->where('dealer_id', $dealer_id)
        ->where('product_id', $product_id)->sum('quantity');  
        
        $datas2 = CustomerLifting::where('year', $years[1])->where('month', $months[1])
        ->where('dealer_id', $dealer_id)
        ->where('product_id', $product_id)->sum('quantity');  
        
        $datas = $datas1+$datas2;
        //dd($datas);
        if($datas){           
                $avglifts = $datas/2;           
                // $res = ($avglifts*90)/100;
                $res = $avglifts;
            // dd($res);
                return $res;
            }        
            return null ;
    }

    // public static function getCurrentMonthLifting($product_id='', $dealer_id='', $lifting_date = '', $lifting_id = '')
    // {
    //     // find the current month lifting of masson
    //     // Get the first day of the current month
    //         //  $firstDayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
    //         $firstDay = date("Y-m-01", strtotime($lifting_date));
    //     // $firstDayOfMonth = '2023-07-01';

    //         // Get the last day of the current month
    //         //  $lastDayOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
    //         $lastDay = date("Y-m-d", strtotime($lifting_date));
    //         //  dd($firstDayOfMonth." ".$lastDayOfMonth);
    //         // $lastDayOfMonth = '2023-07-31';
            
    //         $liftIdArr = DB::table('lifting')
    //             ->where('user_id', $dealer_id)
    //             // ->whereBetween('lifting_date', [$firstDayOfMonth, $lastDayOfMonth])
    //         // ->whereRaw("DATE_FORMAT(lifting_date, '%Y-%m-%d') between '{$firstDayOfMonth}' and '{$lastDayOfMonth}'")          
    //         ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$firstDay}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$lastDay}'")          
    //         ->where('product_id', $product_id)
    //         ->where('id', '<', $lifting_id)
    //             ->pluck('id')
    //             ->toArray();
    //     // dd($liftIdArr);

    //         $datas = Reward::whereIn('lifting_id', $liftIdArr)
    //                 ->where('is_verified', 1)
    //                 ->where('is_bonus', 0)
    //                 ->sum('bag');  
    //     //  dd($datas);
    //     if($datas){   
    //             return $datas;
    //         }        
    //         return 0 ;
    // }


}

