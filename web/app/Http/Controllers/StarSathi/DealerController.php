<?php

namespace App\Http\Controllers\StarSathi;

use App\Models\User;
use App\Models\Setting;
use App\Models\Lifting;
use App\Models\LiftingApprovalHistory;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\MasonLifting;
use App\Models\Product;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\HelperTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StarLinkNotification;
use Illuminate\Support\Facades\Http;

class DealerController extends Controller
{
    use HelperTrait;
    public function authenticate(Request $request)
    {
        $authkey = Setting::where('setting_name', 'star_sathi_auth_key')->pluck('setting_value')->first();
        if(empty($authkey))
        {
            $request->session()->put('error', 'Auth Key Not Found in the System.');
            return redirect(route('dealer.authenticate.error'));
        }
        if($authkey != $request->authkey)
        {
            $request->session()->put('error', 'Invalid Auth Key');
            return redirect(route('dealer.authenticate.error'));
        }
        if(empty($request->sapcode))
        {
            $request->session()->put('error', 'Dealer Code Required.');
            return redirect(route('dealer.authenticate.error'));
        }
        $dealer = User::where('sap_code',$request->sapcode)->first();
        if(!$dealer)
        {
            $request->session()->put('error', 'Invalid SAP Code');
            return redirect(route('dealer.authenticate.error'));
        }
        if(!in_array($dealer->role, [3,4,6]))
        {
            $request->session()->put('error', 'Permission Denied, only Dealer/SD/RSSD have permission.');
            return redirect(route('dealer.authenticate.error'));
        }
        \Auth::login($dealer);
        return redirect(route('dealer.dashboard'));
    }

    public function error(Request $request)
    {
        return view('star-sathi-dealer.error');
    }

    public function dashboard(Request $request)
    {
        return view('star-sathi-dealer.dashboard');
    }

    public function approvedLiftingsByStarSathi(Request $request)
    {
        $dealer =\Auth::user();
        $to_date = date('Y-m-d');
        $from_date = date("Y-m-d", strtotime("31-12-1990"));
        $fromDataVal = '';
        $toDataVal = '';
        $masonDataVal = '';
        $flag = 0;
        if($request->has('fromDate'))
        { 
            if(!empty($request->fromDate))
            {
                $from_date = date("Y-m-d", strtotime($request->fromDate));
                $fromDataVal = $from_date;
                $flag = 1;
            }
        }
        if($request->has('toDate'))
        {
            if(!empty($request->toDate))
            {
                    $to_date = date("Y-m-d", strtotime($request->toDate));
                    $toDataVal = $to_date;
                    $flag = 1;
            }
        }
        if($flag === 1)
        {
            $approvedLiftingsQuery = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 1,
                'seek_approval' => 1,
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'");
        }
        else
        {
            $approvedLiftingsQuery = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 1,
                'seek_approval' => 1,
            ])->with(['product', 'reward']);
        }
        if($request->has('mason'))
        {
            if(!empty($request->mason))
            {
                $mason = User::where('phone', base64_decode($request->mason))->pluck('id')->first();
                $approvedLiftings = $approvedLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                    $q->select('lifting_id')->from('rewards')->where('user_id', $mason);
                })->get();
                $masonDataVal = base64_decode($request->mason);
                $flag = 2;
            }
        }
        if($flag != 2)
        {
            $approvedLiftings = $approvedLiftingsQuery->get();
        }
        
        $approvedLists = [];
        foreach($approvedLiftings as $approvedLifting)
        {
            $points = 0;
            $rewards = Reward::where('lifting_id', $approvedLifting->id)->get();
            if($rewards[0]->is_verified ===0)
            {
                $points = 'In-progress';
            }
            else
            {
                foreach($rewards as $reward)
                {
                    $points+= $reward->point;
                }
            }
            $approvedList = [
                "products" => $approvedLifting->product->name ?? "",
                "no_of_bags" => $approvedLifting->qty ?? "",
                "lifting_date" => $approvedLifting->lifting_date ?? "",
                "point" => $points,
                "mason_name" => $approvedLifting->reward[0]->mason->name ?? "",
                "mason_phone" => $approvedLifting->reward[0]->mason->phone ?? ""
            ];
            $approvedLists[] = $approvedList;
        }
        $masons = MasonLifting::with(['lifting', 'user'])->whereIn(('lifting_id'), function($q) use($dealer){
            $q->select('id')->from('lifting')->where('req_type', 2)->where('req_status', 1)->where('user_id', $dealer->id)->where('seek_approval', 1);
        })->groupBy('mason_id')->get('mason_id');
        return view('star-sathi-dealer.accept-liftings')->with([
            'approvedLists' => $approvedLists,
            'fromDataVal' => $fromDataVal,
            'toDataVal' => $toDataVal,
            'masonDataVal' => $masonDataVal,
            'masons' => $masons,
        ]);
    }

    public function viewAddLiftingByStarSathi(Request $request)
    {
        try
        {
            $lifting = Lifting::where('id', decrypt($request->lifting_id))->with(['product', 'reward'])->first();
        }
        catch(Exception $e)
        {
            Flash::error('Invalid Lifting.');
            return redirect(route('dealer.pending.liftings'));
        }
        if($lifting === null) {
            Flash::error('Lifting not found.');
            return redirect(route('dealer.pending.liftings'));
        }
        if($lifting->req_type != 2 || $lifting->status != 0)
        {
            return response()->json([
                'status' => false, 
                'msg' => 'Wrong Lifting.', 
                'date' => [],
            ]);
        }
        return view('star-sathi-dealer.pending-liftings-accept')->with('lifting', $lifting);
    }
    function addLiftingByStarSathi(Request $request)
    {
        if(!$request->has('lifting_id')) {
            Flash::error('Lifting Id Required');
            return redirect(route('dealer.pending.liftings'));
        }
        $lifting = Lifting::find(decrypt($request->lifting_id));
        if($lifting === null) {
            Flash::error('Invalid Lifting.');
            return redirect(route('dealer.pending.liftings'));
        }
        if($lifting->req_type != 2)
        {
            return response()->json([
                'status' => false, 
                'msg' => 'Wrong Lifting.', 
                'date' => [],
            ]);
        }
        if($lifting->req_status != 0)
        {
            return response()->json([
                'status' => false, 
                'msg' => 'Action has benn already taken in this Lifting.', 
                'date' => [],
            ]);
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
            $lifting->action_taken_at = Carbon::now()->format('y-m-d H:i:s');
            $lifting->action_taken_by = \Auth::user()->id;
            $lifting->save();

            // It is commented out as per client Requirement.
            $isVerified = 0;
            
            // $isVerified = 1;
            // if(($dealerAvailableStock - $currentMonthLiftings) < $lifting->qty)
            // {
            //     $isVerified = 0;
            // }
            $rewards = Reward::where('lifting_id', $lifting->id)->get();
            $masonId = $rewards[0]->user_id;
            $point = 0;
            $bonusPoint = 0;
            foreach($rewards as $reward)
            {
                if($reward->is_bonus == 0){ 
                    $point = $reward->point; 
                } 
                else{ 
                    $bonusPoint = $reward->point;
                }
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
            // if(($dealerAvailableStock - $currentMonthLiftings)< $lifting->qty)
            // {
            //     Flash::error('Lifting is rejected, please contact admin.');
            //     return redirect(route('dealer.pending.liftings'));
                    
            // }else
            // {
                
            //To keep approved history record.
            $liftingApprovalHistory = [
                'lifting_id' => $lifting->id,
                'qty' => $lifting->qty,
                'point' => $point,
                'bonus_point' => $bonusPoint,
                'seek_approval' => 1,
                'seek_approval_by' => $lifting->action_taken_by,
                'seek_approval_from' => $lifting->seek_approval_from,
                'approval_window' => $this->settingVal('setting_name', 'bdo_approval_window'),
                'action_status' => 3,
                'action_taken_by' => \Auth::user()->id,
            ];
            LiftingApprovalHistory::create($liftingApprovalHistory);
            //Send Notification to Mason and Dealer
            if($lifting->req_by == null)
            {
                $msg = "Dealer ".\Auth::user()->name." Accept Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            }
            else
            {
                $msg = "Dealer ".\Auth::user()->name." Accept Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by BD ".($lifting->reward[0]->mason->te_linked->name ?? null)." behalf of Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            }
            $notificationData = [
                "notification_type" => "Lifting",
                "data" => [
                    "msg" => $msg,
                ]
            ];
            Notification::send($lifting->reward[0]->mason ?? null, new StarLinkNotification($notificationData));//Mason
            Notification::send($lifting->user ?? null, new StarLinkNotification($notificationData));//Dealer
            //Send SMS to Mason
            $masonSMS = "Lifting Bags: ".$lifting->qty." ".($lifting->product->name ?? null)." Bags (".$lifting->reward[0]->mason->phone ?? null.") successfully Approved/Rejected: Approved. - Star Link";
            // Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$lifting->reward[0]->mason->phone.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
            Flash::success('lifting Accepted.');
            return redirect(route('dealer.pending.liftings'));  
            // }
        } catch (Exception $e) {
            DB::rollBack();
            $logTable->update([
                'response' => $e->getMessage()
            ]);
            Flash::error($e->getMessage());
            return redirect(route('dealer.pending.liftings'));
        }

    }

    public function pendingLiftingsByStarSathi(Request $request)
    {
        $dealer = \Auth::user();
        $to_date = date('Y-m-d');
        $from_date = date("Y-m-d", strtotime("31-12-1990"));
        $fromDataVal = '';
        $toDataVal = '';
        $masonDataVal = '';
        $flag = 0;
        if($request->has('fromDate'))
        { 
            if(!empty($request->fromDate))
            {
                $from_date = date("Y-m-d", strtotime($request->fromDate));
                $fromDataVal = $from_date;
                $flag = 1;
            }
        }
        if($request->has('toDate'))
        {
            if(!empty($request->toDate))
            {
                    $to_date = date("Y-m-d", strtotime($request->toDate));
                    $toDataVal = $to_date;
                    $flag = 1;
            }
        }
        if($flag === 1)
        {
            $pendingLiftingsQuery = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 0,
                'seek_approval' => 1,
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'");
        }
        else
        {
            $pendingLiftingsQuery = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 0,
                'seek_approval' => 1,
            ])->with(['product', 'reward']);
        }
        if($request->has('mason'))
        {
            if(!empty($request->mason))
            {
                $mason = User::where('phone', base64_decode($request->mason))->pluck('id')->first();
                $pendingLiftings = $pendingLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                    $q->select('lifting_id')->from('rewards')->where('user_id', $mason);
                })->get();
                $masonDataVal = base64_decode($request->mason);
                $flag = 2;
            }
        }
        if($flag != 2)
        {
            $pendingLiftings = $pendingLiftingsQuery->get();
        }
        $pendingLists = [];
        foreach($pendingLiftings as $pendingLifting)
        {
            $pendingList = [
                "lifting_id" => $pendingLifting->id,
                "products" => $pendingLifting->product->name ?? "",
                "no_of_bags" => $pendingLifting->qty ?? "",
                "lifting_date" => $pendingLifting->lifting_date ?? "",
                "mason_name" => $pendingLifting->reward[0]->mason->name ?? "",
                "mason_phone" => $pendingLifting->reward[0]->mason->phone ?? ""
            ];
            $pendingLists[] = $pendingList;
        }
        $masons = MasonLifting::with(['lifting', 'user'])->whereIn(('lifting_id'), function($q) use($dealer){
            $q->select('id')->from('lifting')->where('req_type', 2)->where('req_status', 0)->where('user_id', $dealer->id)->where('seek_approval', 1);
        })->groupBy('mason_id')->get('mason_id');
        return view('star-sathi-dealer.pending-liftings')->with([
            'pendingLists' => $pendingLists,
            'fromDataVal' => $fromDataVal,
            'toDataVal' => $toDataVal,
            'masonDataVal' => $masonDataVal,
            'masons' => $masons,
        ]);
    }
    public function editPendingLiftingsByStarSathi(Request $request)
    {
        try
        {
            $lifting = Lifting::where('id', decrypt($request->id))->with(['product', 'reward'])->first();
        }
        catch(Exception $e)
        {
            Flash::error('Invalid Lifting.');
            return redirect(route('dealer.pending.liftings'));
        }
        if($lifting === null) {
            Flash::error('Lifting not found.');
            return redirect(route('dealer.pending.liftings'));
        }
        if($lifting->req_type != 2 || $lifting->status != 0)
        {
            return response()->json([
                'status' => false, 
                'msg' => 'Wrong Lifting.', 
                'date' => [],
            ]);
        }
        return view('star-sathi-dealer.pending-liftings-edit')->with('lifting', $lifting);
    }
    public function saveEditPendingLiftingsByStarSathi(Request $request)
    {
        try{
            \DB::beginTransaction();
            // return date('Y-m-d', strtotime('-7 days'));
            $lifting = Lifting::find(decrypt($request->id));
            if(!$lifting)
            {
                return response()->json([
                    'status' => false, 
                    'msg' => 'Invalid Lifting Id.', 
                    'date' => [],
                ]);
            }
            if($lifting->req_type != 2 || $lifting->status != 0)
            {
                return response()->json([
                    'status' => false, 
                    'msg' => 'Wrong Lifting.', 
                    'date' => [],
                ]);
            }
            $lifting_qty = $lifting->qty;//Store lifting quantity
            $request->validate([
                'qty'=> 'required|integer|max:'.$lifting_qty.'|gt:0',
            ],
            [
                'qty.required' => 'Quantity is Required.',
                'qty.integer' => 'Quantity should not be pointed value.',
                'qty.max' => 'Quantity should not be greater than '.$lifting_qty.'.',
                'qty.gt' => 'Quantity must be greater than 0.',
            ]);
            $qty = $request->qty;//request quantity
            if($qty < $lifting_qty)
            {
                $point = 0;
                $bonusPoint = 0;
                $product = Product::where('id', $lifting->product_id)->first();
                $reward = Reward::where('lifting_id', $lifting->id)->get();
                if(count($reward) === 2 && $qty <= $product->more_than_bags)
                {
                    Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 1])->delete();
                }
                Reward::where('lifting_id', $lifting->id)->update(['bag' => $qty]);
                Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->update(['point' => $this->getPoint($product->id, $qty)]);
                $rewards = Reward::where('lifting_id', $lifting->id)->get();
                foreach($rewards as $reward)
                {
                    if($reward->is_bonus == 0){ 
                        $point = $reward->point; 
                    } 
                    else{ 
                        $bonusPoint = $reward->point;
                    }
                }
                //testing purpose
                if(\Auth::check() && \Auth::user()->id == 34644)
                {
                    Lifting::where('id', $lifting->id)->update([
                        'qty' => $qty,
                        'seek_approval' => 1,
                        // 'seek_approval_from' => Carbon::now(),
                    ]);
                }
                else
                {
                    Lifting::where('id', $lifting->id)->update([
                        'qty' => $qty,
                        'seek_approval' => 2,
                        'seek_approval_from' => Carbon::now(),
                    ]);
                }
                //testing purpose end
                $teName = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->with('mason')->first();
                if(\Auth::check() && \Auth::user()->id == 34644)
                {
                    $liftingApprovalHistory = [
                        'lifting_id' => $lifting->id,
                        'qty' => $qty,
                        'point' => $point,
                        'bonus_point' => $bonusPoint,
                        'seek_approval' => 1,
                        'seek_approval_by' => \Auth::user()->id,
                        'seek_approval_from' => $lifting->seek_approval_from,
                        'approval_window' => $this->settingVal('setting_name', 'dealer/rssd_approval_window'),
                        'action_status' => 1,
                        'action_taken_by' => \Auth::user()->id,
                    ];
                }
                else
                {
                    $liftingApprovalHistory = [
                        'lifting_id' => $lifting->id,
                        'qty' => $qty,
                        'point' => $point,
                        'bonus_point' => $bonusPoint,
                        'seek_approval' => 2,
                        'seek_approval_by' => $teName->mason->parent ?? 0,
                        'seek_approval_from' => Carbon::now(),
                        'approval_window' => $this->settingVal('setting_name', 'bdo_approval_window'),
                        'action_status' => 1,
                        'action_taken_by' => \Auth::user()->id,
                    ];
                }
                
                LiftingApprovalHistory::create($liftingApprovalHistory);
            }
            
            //Send Notification to Mason and Dealer
            if($lifting->req_by == null)
            {
                $msg = "Dealer ".\Auth::user()->name." Reduced Lifting ".$qty." bags of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            }
            else
            {
                $msg = "Dealer ".\Auth::user()->name." Reduced Lifting ".$qty." bags of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by BD ".($lifting->reward[0]->mason->te_linked->name ?? null)." behalf of Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            }
            $notificationData = [
                "notification_type" => "Lifting",
                "data" => [
                    "msg" => $msg,
                ]
            ];
            Notification::send($lifting->reward[0]->mason ?? null, new StarLinkNotification($notificationData));//Mason
            Notification::send($lifting->user ?? null, new StarLinkNotification($notificationData));//Dealer
            if(\Auth::check() && \Auth::user()->id == 34644)
            {
                //keep log
                $logData = [
                    'user_id' => $lifting->user_id,
                    'request' => json_encode($request->all()),
                    'action' => 'Add Lifting By Star Sathi App',
                    'model_name' => 'Lifting',
                ];
                $logTable = Log::create($logData);
                //keep log
                    $dealerAvailableStock =  $this->availStock($lifting->product_id, $lifting->user_id, $lifting->lifting_date);
                    $currentMonthLiftings =  $this->getCurrentMonthLifting($lifting->product_id, $lifting->user_id, $lifting->lifting_date) ;
                    
                    $lifting->available_stock = $dealerAvailableStock - $currentMonthLiftings;
                    $lifting->req_status = 1;
                    $lifting->action_taken_at = Carbon::now()->format('y-m-d H:i:s');
                    $lifting->action_taken_by = \Auth::user()->id;
                    $lifting->save();
                    
                    // It is commented Out As per Cient Requirement.
                    $isVerified = 0;

                    // $isVerified = 1;
                    // if(($dealerAvailableStock - $currentMonthLiftings) < $lifting->qty)
                    // {
                    //     $isVerified = 0;
                    // }
                    $rewards = Reward::where('lifting_id', $lifting->id)->get();
                    $masonId = $rewards[0]->user_id;
                    $point = 0;
                    $bonusPoint = 0;
                    foreach($rewards as $reward)
                    {
                        if($reward->is_bonus == 0){ 
                            $point = $reward->point; 
                        } 
                        else{ 
                            $bonusPoint = $reward->point;
                        }
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

                    $tables = json_encode([
                        'Lifting' => $lifting,
                        'Reward' => $rewards,
                    ]);
                    $logTable->update([
                        'response' => $tables
                    ]);
                    // if(($dealerAvailableStock - $currentMonthLiftings)< $lifting->qty)
                    // {
                    //     Flash::error('Lifting is rejected, please contact admin.');
                    //     return redirect(route('dealer.pending.liftings'));
                            
                    // }else
                    // {
                        
                    //To keep approved history record.
                    $liftingApprovalHistory = [
                        'lifting_id' => $lifting->id,
                        'qty' => $lifting->qty,
                        'point' => $point,
                        'bonus_point' => $bonusPoint,
                        'seek_approval' => 1,
                        'seek_approval_by' => $lifting->action_taken_by,
                        'seek_approval_from' => $lifting->seek_approval_from,
                        'approval_window' => $this->settingVal('setting_name', 'bdo_approval_window'),
                        'action_status' => 3,
                        'action_taken_by' => \Auth::user()->id,
                    ];
                    LiftingApprovalHistory::create($liftingApprovalHistory);
                    //Send Notification to Mason and Dealer
                    if($lifting->req_by == null)
                    {
                        $msg = "Dealer ".\Auth::user()->name." Accept Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
                    }
                    else
                    {
                        $msg = "Dealer ".\Auth::user()->name." Accept Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by BD ".($lifting->reward[0]->mason->te_linked->name ?? null)." behalf of Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
                    }
                    $notificationData = [
                        "notification_type" => "Lifting",
                        "data" => [
                            "msg" => $msg,
                        ]
                    ];
                    Notification::send($lifting->reward[0]->mason ?? null, new StarLinkNotification($notificationData));//Mason
                    Notification::send($lifting->user ?? null, new StarLinkNotification($notificationData));//Dealer
                    //Send SMS to Mason
                    $masonSMS = "Lifting Bags: ".$lifting->qty." ".($lifting->product->name ?? null)." Bags (".$lifting->reward[0]->mason->phone ?? null.") successfully Approved/Rejected: Approved. - Star Link";
                    // Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$lifting->reward[0]->mason->phone.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
                    
                    // }
            }
            \DB::commit();
            Flash::success('Lifting edited and accepted successfully.');
            return redirect(route('dealer.pending.liftings'));
        }
        catch(\Exception $e)
        {
            \DB::rollback();
            if(!empty($logTable))
            {
                $logTable->update([
                    'response' => $e->getMessage()
                ]);
            }
            Flash::error($e->getMessage());
            return redirect(route('dealer.pending.liftings'));
        }
    }

    public function rejectedLiftingsByStarSathi(Request $request)
    {
        // if($request->has('from_date'))
        $dealer = \Auth::user();
        $to_date = date('Y-m-d');
        $from_date = date("Y-m-d", strtotime("31-12-1990"));
        $fromDataVal = '';
        $toDataVal = '';
        $masonDataVal = '';
        $flag = 0;
        if($request->has('fromDate'))
        { 
            if(!empty($request->fromDate))
            {
                $from_date = date("Y-m-d", strtotime($request->fromDate));
                $fromDataVal = $from_date;
                $flag = 1;
            }
        }
        if($request->has('toDate'))
        {
            if(!empty($request->toDate))
            {
                    $to_date = date("Y-m-d", strtotime($request->toDate));
                    $toDataVal = $to_date;
                    $flag = 1;
            }
        }
        if($flag === 1)
        {
            $rejectedLiftingsQuery = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 2,
                'seek_approval' => 1,
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'");
        }
        else
        {
            $rejectedLiftingsQuery = Lifting::where([
                'user_id' => $dealer->id,
                'req_type' => 2,
                'req_status' => 2,
                'seek_approval' => 1,
            ])->with(['product', 'reward']);
        }
        if($request->has('mason'))
        {
            if(!empty($request->mason))
            {
                $mason = User::where('phone', base64_decode($request->mason))->pluck('id')->first();
                $rejectedLiftings = $rejectedLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                    $q->select('lifting_id')->from('rewards')->where('user_id', $mason);
                })->get();
                $masonDataVal = base64_decode($request->mason);
                $flag = 2;
            }
        }
        if($flag != 2)
        {
            $rejectedLiftings = $rejectedLiftingsQuery->get();
        }
        $rejectedLists = [];
        foreach($rejectedLiftings as $rejectedLifting)
        {
            $rejectedList = [
                "products" => $rejectedLifting->product->name ?? "",
                "no_of_bags" => $rejectedLifting->qty ?? "",
                "lifting_date" => $rejectedLifting->lifting_date ?? "",
                "points" => "In-progress",
                "mason_name" => $rejectedLifting->reward[0]->mason->name ?? "",
                "mason_phone" => $rejectedLifting->reward[0]->mason->phone ?? ""
            ];
            $rejectedLists[] = $rejectedList;
        }
        $masons = MasonLifting::with(['lifting', 'user'])->whereIn(('lifting_id'), function($q) use($dealer){
            $q->select('id')->from('lifting')->where('req_type', 2)->where('req_status', 2)->where('user_id', $dealer->id)->where('seek_approval', 1);
        })->groupBy('mason_id')->get('mason_id');
        return view('star-sathi-dealer.reject-liftings')->with([
            'rejectedLists' => $rejectedLists,
            'fromDataVal' => $fromDataVal,
            'toDataVal' => $toDataVal,
            'masonDataVal' => $masonDataVal,
            'masons' => $masons,
        ]);
    }

    public function viewRejectLiftingByStarSathi(Request $request)
    {
        try
        {
            $lifting = Lifting::where('id', decrypt($request->lifting_id))->with(['product', 'reward'])->first();
        }
        catch(Exception $e)
        {
            Flash::error('Invalid Lifting.');
            return redirect(route('dealer.pending.liftings'));
        }
        if($lifting === null) {
            Flash::error('Lifting not found.');
            return redirect(route('dealer.pending.liftings'));
        }
        if($lifting->req_type != 2 || $lifting->status != 0)
        {
            return response()->json([
                'status' => false, 
                'msg' => 'Wrong Lifting.', 
                'date' => [],
            ]);
        }
        return view('star-sathi-dealer.pending-liftings-reject')->with('lifting', $lifting);
    }
    public function rejectLiftingByStarSathi(Request $request)
    {
        if(!$request->has('lifting_id')) {
            Flash::error('Lifting Id Required');
            return redirect(route('dealer.pending.liftings'));
        }
        if(empty($request->lifting_id)) {
            Flash::error('Lifting Id is Empty.');
            return redirect(route('dealer.pending.liftings'));
        }
        $lifting = Lifting::find(decrypt($request->lifting_id));
        if($lifting)
        {
            if($lifting->req_type != 2)
            {
                return response()->json([
                    'status' => false, 
                    'msg' => 'Wrong Lifting.', 
                    'date' => [],
                ]);
            }
            if($lifting->req_status != 0)
            {
                return response()->json([
                    'status' => false, 
                    'msg' => 'Action has benn already taken in this Lifting.', 
                    'date' => [],
                ]);
            }
            $lifting->req_status = 2;
            $lifting->action_taken_at = Carbon::now()->format('y-m-d H:i:s');
            $lifting->action_taken_by = \Auth::user()->id;
            $lifting->save();
            
            //To keep reject history record.
            $rewards = Reward::where('lifting_id', $lifting->id)->get();
            $point = 0;
            $bonusPoint = 0;
            foreach($rewards as $reward)
            {
                if($reward->is_bonus == 0){ 
                    $point = $reward->point; 
                } 
                else{ 
                    $bonusPoint = $reward->point;
                }
            }
            $liftingApprovalHistory = [
                'lifting_id' => $lifting->id,
                'qty' => $lifting->qty,
                'point' => $point,
                'bonus_point' => $bonusPoint,
                'seek_approval' => 1,
                'seek_approval_by' => $lifting->action_taken_by,
                'seek_approval_from' => $lifting->seek_approval_from,
                'approval_window' => $this->settingVal('setting_name', 'dealer/rssd_approval_window'),
                'action_status' => 4,
                'action_taken_by' => \Auth::user()->id,
            ];
            LiftingApprovalHistory::create($liftingApprovalHistory);
            //Send Notification to Mason and Dealer
            if($lifting->req_by == null)
            {
                $msg = "Dealer ".\Auth::user()->name." Reject Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            }
            else
            {
                $msg = "Dealer ".\Auth::user()->name." Reject Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by BD ".($lifting->reward[0]->mason->te_linked->name ?? null)." behalf of Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            }
            $notificationData = [
                "notification_type" => "Lifting",
                "data" => [
                    "msg" => $msg,
                ]
            ];
            Notification::send($lifting->reward[0]->mason ?? null, new StarLinkNotification($notificationData));//Mason
            Notification::send($lifting->user ?? null, new StarLinkNotification($notificationData));//Dealer
            //Send SMS to Mason
            $masonSMS = "Lifting Bags: ".$lifting->qty." ".($lifting->product->name ?? null)." Bags (".$lifting->reward[0]->mason->phone ?? null.") successfully Approved/Rejected: Rejected. - Star Link";
            // Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$lifting->reward[0]->mason->phone.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
            Flash::success('Lifting Rejected.');
            return redirect(route('dealer.pending.liftings'));
        }
        Flash::error('Invalid Lifting Id.');
        return redirect(route('dealer.pending.liftings'));
        
    }

    public function dealerLogout(Request $request)
    {
      \Auth::logout();
      $request->session()->flush();
      return redirect(route('admin.login')); // redirect the user to the login screen
    }
}
