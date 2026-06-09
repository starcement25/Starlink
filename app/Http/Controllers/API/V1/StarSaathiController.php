<?php

namespace App\Http\Controllers\API\V1;

use Exception;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\User;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\Lifting;
use App\Models\Product;
use App\Models\Setting;
use App\Models\LiftingEnquiry;
use App\Models\MasonDealer;
use App\Traits\HelperTrait;
use App\Models\MasonLifting;
use Illuminate\Http\Request;
use App\Models\CustomerLifting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\DealerLinkageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\LiftingApprovalHistory;
use Illuminate\Support\Facades\Validator;
use App\Models\DealerLinkageRequestHistory;
use App\Http\Requests\StarSaathi\EditLiftingRequest;
use App\Http\Requests\StarSaathi\MasonOptionsRequest;
use App\Http\Requests\StarSaathi\DeleteLiftingRequest;
use App\Http\Requests\StarSaathi\UpdateLiftingRequest;
use App\Http\Requests\StarSaathi\ApproveLiftingRequest;
use App\Http\Requests\DealerLinkings\AcceptDealerLinkingRequest;
use App\Http\Requests\DealerLinkings\RejectDealerLinkingRequest;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StarLinkNotification;
use App\Utils\LocalLanguageTranslation;
use App\Services\GoogleTranslateService;

class StarSaathiController extends Controller
{
    use HelperTrait;

    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }

    public function masonsOptions(MasonOptionsRequest $request)
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
        $req_status = $request->req_status;
        // $teMasons = User::where('parent', \Auth::user()->id)->pluck('id')->toArray();
        $liftingIds = Lifting::where([
            'req_type' => 2,
            'req_status' => $req_status,
            'seek_approval' => 2,
        ])->pluck('id')->toArray();
        $rewardMasonIds = Reward::whereIn('lifting_id', $liftingIds)->groupBy('user_id')->pluck('user_id')->toArray();
        $masons = User::whereIn('id', $rewardMasonIds)->where(['parent' => \Auth::user()->id])->get();
        $masonOptions = []; 
        foreach($masons as $mason)
        {
            $masonOptions []= [
                "key" => $mason->id,
                // "value" => $this->googleTranslate->translateText($mason->name, $targetLanguage)." - ".$mason->phone,
                "value" => $mason->name." - ".$mason->phone,
            ];
        }
        return response()->json([
            'status' => true, 
            // 'msg' => 'Mason options Get Successfull.', 
            'msg' => $this->localLanguageTranslate->translate('Masons_fetched_successfully', $targetLanguage), 
            'data' => [
                'masonOptions' => $masonOptions,
            ],
        ]);
        // return DB::select('SELECT rewards.user_id FROM `lifting` inner join `rewards` on lifting.id = rewards.lifting_id where lifting.req_type = 2 and lifting.req_status = ? and lifting.seek_approval = 2 and rewards.is_bonus = 0 GROUP BY rewards.user_id;', [$request->req_status]);
    }

    public function getTeMason(Request $request)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        // if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
        // {
        //     $targetLanguage = \Auth::user()->preferred_app_lang;
        // }
        if(request()->user() != null && !empty(request()->user()->preferred_app_lang))
        {
            $targetLanguage = request()->user()->preferred_app_lang;
        }
        // $teId = \Auth::user()->id;
        $teId = request()->user()->id;
        $masonOptions = [];
        
        if(!empty($targetLanguage))  
        {
            $page = 1;
            if($request->has("page") && $request->page != null)
            {
                $page = $request->page;
            }
            if($page < 1)
            {
                return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 404);
            }
            $limit = 10000;
            $fetchDataFrom = $limit * ($page - 1);
            $masons = User::where(['parent' => $teId, 'status' => 1])->skip($fetchDataFrom)->take($limit)->get();
        }
        else
        {
            $masons = User::where(['parent' => $teId, 'status' => 1])->get();
        }
        foreach($masons as $mason)
        {
            if(!empty($targetLanguage))  
            {
                $masonOptions []= [
                    "key" => $mason->id,
                    "keyword" => $mason->name." - ".$mason->phone,
                    // "value" => $this->googleTranslate->translateText($mason->name, $targetLanguage)." - ".$mason->phone,
                    "value" => $mason->name." - ".$mason->phone,
                ];
                
            }
            else
            {
                $masonOptions []= [
                    "key" => $mason->id,
                    "value" => $mason->name." - ".$mason->phone,
                ];
            }
        }
        if($masons->isEmpty())
        {
            return response()->json([
                'status' => false, 
                'msg' => $this->localLanguageTranslate->translate('No_masons_found', $targetLanguage), 
                'data' => [],
            ], 404);
        }
        return response()->json([
            'status' => true, 
            'msg' => $this->localLanguageTranslate->translate('Masons_fetched_successfully', $targetLanguage), 
            'data' => [
                'masonOptions' => $masonOptions,
            ],
        ]);
    }
    public function pendingLiftingsByStarSathi(Request $request)
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
        $te = \Auth::user();
        $to_date = date('Y-m-d');
        $from_date = date("Y-m-d", strtotime("31-12-1990"));
        $fromDataVal = '';
        $toDataVal = '';
        $masonSelected = '';
        $flag = 0;
        $pendingLiftingsQuery = null;
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
                'req_type' => 2,
                'req_status' => 0,
                'seek_approval' => 2,
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'")->orderBy('id', 'DESC');
        }
        else
        {
            $pendingLiftingsQuery = Lifting::where([
                'req_type' => 2,
                'req_status' => 0,
                'seek_approval' => 2,
            ])->with(['product', 'reward'])->orderBy('id', 'DESC');
        }
        if($request->has('mason'))
        {
            if(!empty($request->mason))
            {
                $mason = User::where('id', $request->mason)->pluck('id')->first();
                $pendingLiftingsQuery = $pendingLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                    $q->select('lifting_id')->from('rewards')->where('user_id', $mason);
                });
                $masonSelected = $request->mason;
                $flag = 2;
            }
        }
        if($flag != 2)
        {
            $mason = User::where('parent', $te->id)->pluck('id')->toArray();
            $pendingLiftingsQuery = $pendingLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                $q->select('lifting_id')->from('rewards')->whereIn('user_id', $mason)->where('is_bonus', 0);
            });
        }
        $pendingLists = [];
        //Pagination
        if(!empty($pendingLiftingsQuery))
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
            $dataCount = $pendingLiftingsQuery->count();
            $limit = 10;
            $totalPage = ceil($dataCount / $limit);
            if($page > $totalPage)
            {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
            }
            $fetchDataFrom = $limit * ($page - 1);
            $pendingLiftings = $pendingLiftingsQuery->skip($fetchDataFrom)->take($limit)->get();
        }
        //end of pagination
        foreach($pendingLiftings as $pendingLifting)
        {
            // $masonSubmitedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $pendingLifting->id,
            //     'action_status' => 0
            // ])->first()->qty;
            // $dealerEditedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $pendingLifting->id,
            //     'action_status' => 1,
            //     'action_taken_by' => $pendingLifting->dealer->id
            // ])->first()->qty ?? "";
            // $bdEditedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $pendingLifting->id,
            //     'action_status' => 1,
            //     'action_taken_by' => \Auth::user()->id
            // ])->first()->qty ?? "";
            $liftingApprovalHistoryRecords = LiftingApprovalHistory::where([
                'lifting_id' => $pendingLifting->id
            ])->get();
            $masonSubmitedQty = "";
            $masonSubmitedQtyChecked = false;
            $dealerEditedQty = "";
            $dealerEditedQtyChecked = false;
            $bdEditedQty = "";
            $bdEditedQtyChecked = false;
            foreach($liftingApprovalHistoryRecords as $liftingApprovalHistoryRecord)
            {
                if($liftingApprovalHistoryRecord->action_status == 0 && !$masonSubmitedQtyChecked)
                {
                    $masonSubmitedQty = $liftingApprovalHistoryRecord->qty;
                    $masonSubmitedQtyChecked = true;
                }
                if($liftingApprovalHistoryRecord->action_status == 1 && $liftingApprovalHistoryRecord->action_taken_by == $pendingLifting->dealer->id && !$dealerEditedQtyChecked)
                {
                    $dealerEditedQty = $liftingApprovalHistoryRecord->qty;
                    $dealerEditedQtyChecked = true;
                }
                if($liftingApprovalHistoryRecord->action_status == 1 && $liftingApprovalHistoryRecord->action_taken_by == \Auth::user()->id && !$bdEditedQtyChecked)
                {
                    $bdEditedQty = $liftingApprovalHistoryRecord->qty;
                    $bdEditedQtyChecked = true;
                }
            }
            $pendingList = ["dataItem" =>
                [
                    [
                        "key" => "lifting_id",
                        "value" => $pendingLifting->id
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Product", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($pendingLifting->product->name ?? "", $targetLanguage)
                        "value" => $pendingLifting->product->name ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("No_of_bags", $targetLanguage),
                        "value" => $pendingLifting->qty ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Lifting_date", $targetLanguage),
                        "value" => $pendingLifting->lifting_date ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_name", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($pendingLifting->reward->mason->name ?? "", $targetLanguage)
                        "value" => $pendingLifting->reward->mason->name ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_phone", $targetLanguage),
                        "value" => $pendingLifting->reward->mason->phone ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_name", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($pendingLifting->dealer->name ?? "", $targetLanguage)
                        "value" => $pendingLifting->dealer->name ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_code", $targetLanguage),
                        "value" => $pendingLifting->dealer->emp_code ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_submitted_qty", $targetLanguage),
                        "value" => $masonSubmitedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_edited_qty", $targetLanguage),
                        "value" => $dealerEditedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("BD_edited_qty", $targetLanguage),
                        "value" => $bdEditedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Pending_qty", $targetLanguage),
                        "value" => $pendingLifting->qty ?? ""
                    ],
                ]
            ];
            $pendingLists[] = $pendingList;
        }
        return response()->json([
            'status' => true, 
            'msg' => $this->localLanguageTranslate->translate('Lists_of_pending_liftings_fetched_successfull', $targetLanguage), 
            'data' => [
                'pendingLists' => $pendingLists,
                'fromDataVal' => $fromDataVal,
                'toDataVal' => $toDataVal,
                'masonSelected' => $masonSelected,
            ],
        ]);
    }
    public function editLiftingByStarSathi(EditLiftingRequest $request)
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
        try
        {
            $lifting = Lifting::where('id', $request->lifting_id)->with(['product', 'reward'])->first();
        }
        catch(Exception $e)
        {
            return response()->json([
                'status' => false, 
                'msg' => $this->localLanguageTranslate->translate('Invalid_lifting', $targetLanguage), 
                'data' => [],
            ], 404);
        }
        if($lifting === null) {
            return response()->json([
                'status' => false, 
                'msg' => $this->localLanguageTranslate->translate('Lifting_not_found', $targetLanguage), 
                'data' => [],
            ], 404);
        }
        if($lifting->req_type != 2 || $lifting->status != 0)
        {
            return response()->json([
                'status' => false, 
                'msg' => $this->localLanguageTranslate->translate('Wrong_lifting', $targetLanguage), 
                'data' => [],
            ]);
        }
        return response()->json([
            'status' => true, 
            'msg' => $this->localLanguageTranslate->translate('Details_of_liftings_got_successfully', $targetLanguage), 
            'data' => [
                // 'product' => $this->googleTranslate->translateText($lifting->product->name ?? "", $targetLanguage),
                'product' => $lifting->product->name ?? "",
                'lifting_date' => $lifting->lifting_date ?? "",
                // 'mason_name' => $this->googleTranslate->translateText($lifting->reward->mason->name ?? "", $targetLanguage),
                'mason_name' => $lifting->reward->mason->name ?? "",
                'mason_phone' => $lifting->reward->mason->phone ?? "",
                'no_of_bags' => $lifting->qty ?? "",
            ],
        ]);
    
    }
    public function saveEditLiftingsByStarSathi(UpdateLiftingRequest $request)
    {
        return response()->json([
            'status' => false, 
            'msg' => 'This API is Blocked by Dev.', 
            'data' => [],
        ]);
        $qty = $request->qty;
        $lifting = Lifting::find($request->lifting_id);
        if(!$lifting)
        {
            return response()->json([
                'status' => false, 
                'msg' => 'Invalid Lifting Id.', 
                'data' => [],
            ]);
        }
        $masonId = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->first()->user_id;
        $mason = User::find($masonId)->parent == \Auth::user()->id ? 1 : 0;
        if($lifting->req_type != 2 || $lifting->status != 0 || $lifting->seek_approval != 2 || $mason === 0)
        {
            return response()->json([
                'status' => false, 
                'msg' => 'Wrong Lifting.', 
                'data' => [],
            ]);
        }
        if($qty > $lifting->qty)
        {
            return response()->json([
                'status' => false, 
                'msg' => 'Quantity should not be greater than '.$lifting->qty.'.', 
                'data' => [],
            ]);
        }
        if($qty < $lifting->qty)
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
            Lifting::where('id', $lifting->id)->update([
                'qty' => $qty,
            ]);
            $teName = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->with('mason')->first();
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
            LiftingApprovalHistory::create($liftingApprovalHistory);
        }
        return response()->json([
            'status' => true, 
            'msg' => 'Lifting Edited successfully..', 
            'data' => [],
        ]);
    }
    public function approvedLiftingsByStarSathi(Request $request)
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
        $te = \Auth::user();
        $to_date = date('Y-m-d');
        $from_date = date("Y-m-d", strtotime("31-12-1990"));
        $fromDataVal = '';
        $toDataVal = '';
        $masonSelected = '';
        $flag = 0;
        $acceptLiftingsQuery = null;
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
            $acceptLiftingsQuery = Lifting::where([
                'req_type' => 2,
                'req_status' => 1,
                'seek_approval' => 2,
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'")->orderBy('id', 'DESC');
        }
        else
        {
            $acceptLiftingsQuery = Lifting::where([
                'req_type' => 2,
                'req_status' => 1,
                'seek_approval' => 2,
            ])->with(['product', 'reward'])->orderBy('id', 'DESC');
        }
        if($request->has('mason'))
        {
            if(!empty($request->mason))
            {
                $mason = User::where('id', $request->mason)->pluck('id')->first();
                $acceptLiftingsQuery = $acceptLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                    $q->select('lifting_id')->from('rewards')->where('user_id', $mason);
                });
                $masonSelected = $request->mason;
                $flag = 2;
            }
        }
        if($flag != 2)
        {
            $mason = User::where('parent', $te->id)->pluck('id')->toArray();
            $acceptLiftingsQuery = $acceptLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                $q->select('lifting_id')->from('rewards')->whereIn('user_id', $mason)->where('is_bonus', 0);
            });
        }
        $acceptLists = [];
        //Pagination
        if(!empty($acceptLiftingsQuery))
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
            $dataCount = $acceptLiftingsQuery->count();
            $limit = 10;
            $totalPage = ceil($dataCount / $limit);
            if($page > $totalPage)
            {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
            }
            $fetchDataFrom = $limit * ($page - 1);
            $acceptLiftings = $acceptLiftingsQuery->skip($fetchDataFrom)->take($limit)->get();
        }
        //end of pagination
        foreach($acceptLiftings as $acceptLifting)
        {
            $points = 0;
            $rewards = Reward::where('lifting_id', $acceptLifting->id)->get();
            if($rewards[0]->is_verified === 0)
            {
                $points = $this->localLanguageTranslate->translate('In-progress', $targetLanguage);
            }
            else
            {
                foreach($rewards as $reward)
                {
                    $points+= $reward->point;
                }
            }
            // $masonSubmitedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $acceptLifting->id,
            //     'action_status' => 0
            // ])->first()->qty;
            // $dealerEditedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $acceptLifting->id,
            //     'action_status' => 1,
            //     'action_taken_by' => $acceptLifting->dealer->id
            // ])->first()->qty ?? "";
            // $bdEditedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $acceptLifting->id,
            //     'action_status' => 1,
            //     'action_taken_by' => \Auth::user()->id
            // ])->first()->qty ?? "";
            // $approvedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $acceptLifting->id,
            //     'action_status' => 3
            // ])->first()->qty ?? "";
            $liftingApprovalHistoryRecords = LiftingApprovalHistory::where([
                'lifting_id' => $acceptLifting->id
            ])->get();
            $masonSubmitedQty = "";
            $masonSubmitedQtyChecked = false;
            $dealerEditedQty = "";
            $dealerEditedQtyChecked = false;
            $bdEditedQty = "";
            $bdEditedQtyChecked = false;
            $approvedQty = "";
            $approvedQtyChecked = false;
            foreach($liftingApprovalHistoryRecords as $liftingApprovalHistoryRecord)
            {
                if($liftingApprovalHistoryRecord->action_status == 0 && !$masonSubmitedQtyChecked)
                {
                    $masonSubmitedQty = $liftingApprovalHistoryRecord->qty;
                    $masonSubmitedQtyChecked = true;
                }
                if($liftingApprovalHistoryRecord->action_status == 1 && $liftingApprovalHistoryRecord->action_taken_by == $acceptLifting->dealer->id && !$dealerEditedQtyChecked)
                {
                    $dealerEditedQty = $liftingApprovalHistoryRecord->qty;
                    $dealerEditedQtyChecked = true;
                }
                if($liftingApprovalHistoryRecord->action_status == 1 && $liftingApprovalHistoryRecord->action_taken_by == \Auth::user()->id && !$bdEditedQtyChecked)
                {
                    $bdEditedQty = $liftingApprovalHistoryRecord->qty;
                    $bdEditedQtyChecked = true;
                }
                if($liftingApprovalHistoryRecord->action_status == 3 && !$approvedQtyChecked)
                {
                    $approvedQty = $liftingApprovalHistoryRecord->qty;
                    $approvedQtyChecked = true;
                }
            }
            $acceptList = ["dataItem" =>
                [
                    [
                        "key" => "lifting_id",
                        "value" =>  $acceptLifting->id,
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Product", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($acceptLifting->product->name ?? "", $targetLanguage)
                        "value" => $acceptLifting->product->name ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("No_of_bags", $targetLanguage),
                        "value" => $acceptLifting->qty ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Lifting_date", $targetLanguage),
                        "value" => $acceptLifting->lifting_date ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Point", $targetLanguage),
                        "value" => $points,
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_name", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($acceptLifting->reward->mason->name ?? "", $targetLanguage)
                        "value" => $acceptLifting->reward->mason->name ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_phone", $targetLanguage),
                        "value" => $acceptLifting->reward->mason->phone ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_name", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($acceptLifting->dealer->name ?? "", $targetLanguage)
                        "value" => $acceptLifting->dealer->name ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_code", $targetLanguage),
                        "value" => $acceptLifting->dealer->emp_code ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_submitted_qty", $targetLanguage),
                        "value" => $masonSubmitedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_edited_qty", $targetLanguage),
                        "value" => $dealerEditedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("BD_edited_qty", $targetLanguage),
                        "value" => $bdEditedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Approved_qty", $targetLanguage),
                        "value" => $approvedQty
                    ],
                ]
            ];
            $acceptLists[] = $acceptList;
        }
        return response()->json([
            'status' => true, 
            'msg' => $this->localLanguageTranslate->translate('Lists_of_accepted_liftings_fetched_Successfully', $targetLanguage), 
            'data' => [
                'acceptedLists' => $acceptLists,
                'fromDataVal' => $fromDataVal,
                'toDataVal' => $toDataVal,
                'masonSelected' => $masonSelected,
            ],
        ]);
    }
    function approveLiftingByStarSathi(ApproveLiftingRequest $request)
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
        $lifting = Lifting::find($request->lifting_id);
        if(!$lifting) {
            return response()->json([
                'status' => false, 
                'msg' => $this->localLanguageTranslate->translate('Invalid_lifting', $targetLanguage), 
                'data' => [],
            ]);
        }
        $masonId = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->first()->user_id;
        $mason = User::find($masonId)->parent == \Auth::user()->id ? 1 : 0;
        if($lifting->req_type != 2 || $lifting->seek_approval != 2 || $mason === 0)
        {
            return response()->json([
                'status' => false, 
                'msg' => $this->localLanguageTranslate->translate('Wrong_lifting', $targetLanguage), 
                'data' => [],
            ]);
        }
        if($lifting->req_status != 0)
        {
            return response()->json([
                'status' => false, 
                'msg' => $this->localLanguageTranslate->translate('Action_has_been_already_taken_in_this_lifting', $targetLanguage), 
                'data' => [],
            ]);
        }
        //keep log
        $logData = [
            'user_id' => Auth::user()->id,
            'request' => json_encode($request->all()),
            'model_name' => 'Lifting, lifting_approval_history and Reward'
        ];
        $qty = $lifting->qty;
        if($request->has('qty'))//means user edit the quantity
        {
            $qty = $request->qty;
            if($qty > $lifting->qty)
            {
                return response()->json([
                    'status' => false, 
                    'msg' => $this->googleTranslate->translateText('Quantity should not be greater than '.$lifting->qty.'.', $targetLanguage), 
                    'data' => [],
                ]);
            }
            if($qty < $lifting->qty)
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
                Lifting::where('id', $lifting->id)->update([
                    'qty' => $qty,
                ]);
                $teName = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->with('mason')->first();
                $liftingApprovalHistory = [
                    'lifting_id' => $lifting->id,
                    'qty' => $qty,
                    'point' => $point,
                    'bonus_point' => $bonusPoint,
                    'seek_approval' => 2,
                    'seek_approval_by' => $teName->mason->parent ?? 0,
                    'seek_approval_from' => $lifting->seek_approval_from,
                    'approval_window' => $this->settingVal('setting_name', 'bdo_approval_window'),
                    'action_status' => 1,
                    'action_taken_by' => \Auth::user()->id,
                ];
                LiftingApprovalHistory::create($liftingApprovalHistory);
            }
            $logData['action'] = 'Edit and Add Lifting By TE/BDO';
            //Send Notification to Mason and Dealer
            if($lifting->req_by == null)
            {
                $msg = "BD ".\Auth::user()->name." Reduced Lifting to ".$qty." of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by Mason ".($lifting->reward->mason->name ?? null)." Phone No. ".($lifting->reward->mason->phone ?? null);
            }
            // else
            // {
            //     $msg = "Dealer ".\Auth::user()->name." Dealer SAP Code ".\Auth::user()->sap_code." Reject Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by BD ".($lifting->reward[0]->mason->te_linked->name ?? null)." BD Code ".$lifting->reward[0]->mason->te_linked->emp_code." behalf of Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            // }
            $notificationData = [
                "notification_type" => "Lifting",
                "data" => [
                    "msg" => $msg,
                ]
            ];
            Notification::send($lifting->reward->mason ?? null, new StarLinkNotification($notificationData));//Mason
            Notification::send($lifting->user ?? null, new StarLinkNotification($notificationData));//Dealer
        }
        else
        {
            $logData['action'] = 'Add Lifting By TE/BDO';
        }
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
            $isVerified = 0;

            // As per discussion with client following condition is made.
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
            
            $liftingApprovalHistory = [
                'lifting_id' => $lifting->id,
                'qty' => $qty,
                'point' => $point,
                'bonus_point' => $bonusPoint,
                'seek_approval' => 2,
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
                $msg = "BD ".\Auth::user()->name." Approved Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by Mason ".($lifting->reward->mason->name ?? null)." Phone No. ".($lifting->reward->mason->phone ?? null);
            }
            // else
            // {
            //     $msg = "Dealer ".\Auth::user()->name." Dealer SAP Code ".\Auth::user()->sap_code." Reject Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by BD ".($lifting->reward[0]->mason->te_linked->name ?? null)." BD Code ".$lifting->reward[0]->mason->te_linked->emp_code." behalf of Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            // }
            $notificationData = [
                "notification_type" => "Lifting",
                "data" => [
                    "msg" => $msg,
                ]
            ];
            Notification::send($lifting->reward->mason ?? null, new StarLinkNotification($notificationData));//Mason
            Notification::send($lifting->user ?? null, new StarLinkNotification($notificationData));//Dealer
            //Send SMS to Mason
            $masonSMS = "Lifting Bags: ".$lifting->qty." ".($lifting->product->name ?? null)." Bags (".$lifting->reward->mason->phone ?? null.") successfully Approved/Rejected: Approved. - Star Link";
            // Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$lifting->reward->mason->phone.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
            // $masonSMS = "Lifting Bags: ".$lifting->qty." ".($lifting->product->name ?? null)." Bags (".$lifting->reward[0]->mason->phone ?? null.") successfully Approved/Rejected: Approved. - Star Link";
            // Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$lifting->reward[0]->mason->phone.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
            return response()->json([
                'status' => true, 
                'msg' => $this->localLanguageTranslate->translate('Lifting_approved', $targetLanguage), 
                'data' => [],
            ]);  
            // }
        } catch (Exception $e) {
            DB::rollBack();
            $logTable->update([
                'response' => $e->getMessage()
            ]);
            return response()->json([
                'status' => false, 
                'msg' => $this->googleTranslate->translateText($e->getMessage(), $targetLanguage), 
                'data' => [],
            ]);
        }

    }
    public function rejectLiftingsByStarSathi(Request $request)
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
        $te = \Auth::user();
        $to_date = date('Y-m-d');
        $from_date = date("Y-m-d", strtotime("31-12-1990"));
        $fromDataVal = '';
        $toDataVal = '';
        $masonSelected = '';
        $flag = 0;
        $rejectLiftingsQuery = null;
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
            $historyUpto = $this->settingVal('setting_name', 'history_upto');
            if(!empty($historyUpto) && $historyUpto > 0)
            {
                $from_date = $from_date < Carbon::now()->subDays($historyUpto);
                $from_date = Carbon::now()->subDays($historyUpto);
            }
            $rejectLiftingsQuery = Lifting::where([
                'req_type' => 2,
                'req_status' => 2,
                'seek_approval' => 2,
            ])->with(['product', 'reward'])
            ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$from_date}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$to_date}'")->orderBy('id', 'DESC');
        }
        else
        {
            $historyUpto = $this->settingVal('setting_name', 'history_upto');
            if(!empty($historyUpto) && $historyUpto > 0)
            {
                $historyUptoDate = Carbon::now()->subDays($historyUpto);
                $rejectLiftingsQuery = Lifting::where([
                    'req_type' => 2,
                    'req_status' => 2,
                    'seek_approval' => 2,
                ])->with(['product', 'reward'])
                ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$historyUptoDate}'")->orderBy('id', 'DESC');
            }
            else
            {
                $rejectLiftingsQuery = Lifting::where([
                    'req_type' => 2,
                    'req_status' => 2,
                    'seek_approval' => 2,
                ])->with(['product', 'reward'])->orderBy('id', 'DESC');
            }
        }
        if($request->has('mason'))
        {
            if(!empty($request->mason))
            {
                $mason = User::where('id', $request->mason)->pluck('id')->first();
                $rejectLiftingsQuery = $rejectLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                    $q->select('lifting_id')->from('rewards')->where('user_id', $mason);
                });
                $masonSelected = $request->mason;
                $flag = 2;
            }
        }
        if($flag != 2)
        {
            $mason = User::where('parent', $te->id)->pluck('id')->toArray();
            $rejectLiftingsQuery = $rejectLiftingsQuery->whereIn(DB::raw("`lifting`.`id`"), function($q) use($mason){
                $q->select('lifting_id')->from('rewards')->whereIn('user_id', $mason)->where('is_bonus', 0);
            });
        }
        $rejectLists = [];
        //Pagination
        if(!empty($rejectLiftingsQuery))
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
            $dataCount = $rejectLiftingsQuery->count();
            $limit = 10;
            $totalPage = ceil($dataCount / $limit);
            if($page > $totalPage)
            {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
            }
            $fetchDataFrom = $limit * ($page - 1);
            $rejectLiftings = $rejectLiftingsQuery->skip($fetchDataFrom)->take($limit)->get();
        }
        //end of pagination
        foreach($rejectLiftings as $rejectLifting)
        {
            // $masonSubmitedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $rejectLifting->id,
            //     'action_status' => 0
            // ])->first()->qty;
            // $dealerEditedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $rejectLifting->id,
            //     'action_status' => 1,
            //     'action_taken_by' => $rejectLifting->dealer->id
            // ])->first()->qty ?? "";
            // $bdEditedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $rejectLifting->id,
            //     'action_status' => 1,
            //     'action_taken_by' => \Auth::user()->id
            // ])->first()->qty ?? "";
            // $rejectedQty = LiftingApprovalHistory::where([
            //     'lifting_id' => $rejectLifting->id,
            //     'action_status' => 4
            // ])->first()->qty ?? "";
            $liftingApprovalHistoryRecords = LiftingApprovalHistory::where([
                'lifting_id' => $rejectLifting->id
            ])->get();
            $masonSubmitedQty = "";
            $masonSubmitedQtyChecked = false;
            $dealerEditedQty = "";
            $dealerEditedQtyChecked = false;
            $bdEditedQty = "";
            $bdEditedQtyChecked = false;
            $rejectedQty = "";
            $rejectedQtyChecked = false;
            foreach($liftingApprovalHistoryRecords as $liftingApprovalHistoryRecord)
            {
                if($liftingApprovalHistoryRecord->action_status == 0 && !$masonSubmitedQtyChecked)
                {
                    $masonSubmitedQty = $liftingApprovalHistoryRecord->qty;
                    $masonSubmitedQtyChecked = true;
                }
                if($liftingApprovalHistoryRecord->action_status == 1 && $liftingApprovalHistoryRecord->action_taken_by == $rejectLifting->dealer->id && !$dealerEditedQtyChecked)
                {
                    $dealerEditedQty = $liftingApprovalHistoryRecord->qty;
                    $dealerEditedQtyChecked = true;
                }
                if($liftingApprovalHistoryRecord->action_status == 1 && $liftingApprovalHistoryRecord->action_taken_by == \Auth::user()->id && !$bdEditedQtyChecked)
                {
                    $bdEditedQty = $liftingApprovalHistoryRecord->qty;
                    $bdEditedQtyChecked = true;
                }
                if($liftingApprovalHistoryRecord->action_status == 4 && !$rejectedQtyChecked)
                {
                    $rejectedQty = $liftingApprovalHistoryRecord->qty;
                    $rejectedQtyChecked = true;
                }
            }
            $rejectList = ["dataItem" =>
                [
                    [
                        "key" => "lifting_id",
                        "value" => $rejectLifting->id,
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Product", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($rejectLifting->product->name ?? "", $targetLanguage),
                        "value" => $rejectLifting->product->name ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("No_of_bags", $targetLanguage),
                        "value" => $rejectLifting->qty ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Lifting_date", $targetLanguage),
                        "value" => $rejectLifting->lifting_date ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Point", $targetLanguage),
                        "value" => $this->localLanguageTranslate->translate('In-progress', $targetLanguage),
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_name", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($rejectLifting->reward->mason->name ?? "", $targetLanguage),
                        "value" => $rejectLifting->reward->mason->name ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_phone", $targetLanguage),
                        "value" => $rejectLifting->reward->mason->phone ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_name", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($rejectLifting->dealer->name ?? "", $targetLanguage)
                        "value" => $rejectLifting->dealer->name ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_code", $targetLanguage),
                        "value" => $rejectLifting->dealer->emp_code ?? ""
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_submitted_qty", $targetLanguage),
                        "value" => $masonSubmitedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_edited_qty", $targetLanguage),
                        "value" => $dealerEditedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("BD_edited_qty", $targetLanguage),
                        "value" => $bdEditedQty
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Rejected_qty", $targetLanguage),
                        "value" => $rejectedQty
                    ],
                ]
            ];
            $rejectLists[] = $rejectList;
        }
        return response()->json([
            'status' => true, 
            'msg' => $this->localLanguageTranslate->translate('Lists_of_rejected_liftings_fetched_Successfully', $targetLanguage), 
            'data' => [
                'rejectedLists' => $rejectLists,
                'fromDataVal' => $fromDataVal,
                'toDataVal' => $toDataVal,
                'masonSelected' => $masonSelected,
            ],
        ]);
    }
    public function rejectLiftingByStarSathi(DeleteLiftingRequest $request)
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
        $lifting = Lifting::find($request->lifting_id);
        if($lifting)
        {
            $masonId = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->first()->user_id;
            $mason = User::find($masonId)->parent == \Auth::user()->id ? 1 : 0;
            if($lifting->req_type != 2 || $lifting->seek_approval != 2 || $mason === 0)
            {
                return response()->json([
                    'status' => false, 
                    'msg' => $this->localLanguageTranslate->translate('Wrong_lifting', $targetLanguage), 
                    'data' => [],
                ]);
            }
            if($lifting->req_status != 0)
            {
                return response()->json([
                    'status' => false, 
                    'msg' => $this->localLanguageTranslate->translate('Action_has_been_already_taken_in_this_lifting', $targetLanguage), 
                    'data' => [],
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
                'seek_approval' => 2,
                'seek_approval_by' => $lifting->action_taken_by,
                'seek_approval_from' => $lifting->seek_approval_from,
                'approval_window' => $this->settingVal('setting_name', 'bdo_approval_window'),
                'action_status' => 4,
                'action_taken_by' => \Auth::user()->id,
            ];
            LiftingApprovalHistory::create($liftingApprovalHistory);
            //Send Notification to Mason and Dealer
            if($lifting->req_by == null)
            {
                $msg = "BD ".\Auth::user()->name." Reject Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by Mason ".($lifting->reward->mason->name ?? null)." Phone No. ".($lifting->reward->mason->phone ?? null);
            }
            // else
            // {
            //     $msg = "Dealer ".\Auth::user()->name." Dealer SAP Code ".\Auth::user()->sap_code." Reject Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by BD ".($lifting->reward[0]->mason->te_linked->name ?? null)." BD Code ".$lifting->reward[0]->mason->te_linked->emp_code." behalf of Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
            // }
            $notificationData = [
                "notification_type" => "Lifting",
                "data" => [
                    "msg" => $msg,
                ]
            ];
            Notification::send($lifting->reward->mason ?? null, new StarLinkNotification($notificationData));//Mason
            Notification::send($lifting->user ?? null, new StarLinkNotification($notificationData));//Dealer
            //Send SMS to Mason
            $masonSMS = "Lifting Bags: ".$lifting->qty." ".($lifting->product->name ?? null)." Bags (".$lifting->reward->mason->phone ?? null.") successfully Approved/Rejected: Rejected. - Star Link";
            // Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$lifting->reward->mason->phone.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
            return response()->json([
                'status' => true, 
                'msg' => $this->localLanguageTranslate->translate('Lifting_rejected_successfully', $targetLanguage), 
                'data' => [],
            ]);
        }
        return response()->json([
            'status' => false, 
            'msg' => $this->localLanguageTranslate->translate('Invalid_lifting', $targetLanguage), 
            'data' => [],
        ]);
        
    }
    // Dealer Linking Actions
    public function getDealerLinkingRequest(Request $request)
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
        $te = \Auth::user();
        $masons = User::where([
            "parent" => $te->id,
            "role" => 2,
        ])->pluck('id')->toArray();
        $historyUpto = $this->settingVal('setting_name', 'history_upto');
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
            $limit = 6;
            $fetchDataFrom = $limit * ($page - 1);
            if($request->status == 2 && !empty($historyUpto) && $historyUpto > 0 )
            {
                $dealerLinkageRequests = DealerLinkageRequest::where([
                    "status" => $request->status
                ])->whereIn('user_id', $masons)->where("created_at", ">=", Carbon::now()->subDays($historyUpto))->orderBy("updated_at", "DESC")->skip($fetchDataFrom)->take($limit)->get();
            }
            else
            {
                $dealerLinkageRequests = DealerLinkageRequest::where([
                    "status" => $request->status
                ])->whereIn('user_id', $masons)->orderBy("updated_at", "DESC")->skip($fetchDataFrom)->take($limit)->get();
            }
        }
        else
        {
            $dealerLinkageRequests = DealerLinkageRequest::where([
                "status" => $request->status
            ])->whereIn('user_id', $masons)->orderBy("updated_at", "DESC")->get();
        }
        $lists = [];
        if($dealerLinkageRequests->isEmpty())
        {
            return response()->json([
                'status' => false,
                'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage),
                'data' => []
            ], 404);
        }
        foreach($dealerLinkageRequests as $dealerLinkageRequest)
        {
            $mason = User::find($dealerLinkageRequest->user_id);
            $dealer = User::find($dealerLinkageRequest->dealer_id);
            $list = ["dataItem" =>
                [
                    [
                        "key" => "id",
                        "value" => $dealerLinkageRequest->id,
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_name", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($mason->name ?? "", $targetLanguage),
                        "value" => $mason->name ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Contractor_phone", $targetLanguage),
                        "value" => $mason->phone ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_name", $targetLanguage),
                        // "value" => $this->googleTranslate->translateText($dealer->name ?? "", $targetLanguage),
                        "value" => $dealer->name ?? "",
                    ],
                    [
                        "key" => $this->localLanguageTranslate->translate("Dealer_SAP_code", $targetLanguage),
                        "value" => $dealer->sap_code ?? "",
                    ]
                ]
            ];
            $lists[] = $list;
        }
        return response()->json([
            'status' => true,
            'msg' => $this->localLanguageTranslate->translate('Requests_fetched_successfully', $targetLanguage),
            'data' => [
                'lists' => $lists
            ]
        ]);
    }
    public function acceptDealerLinkingRequest(AcceptDealerLinkingRequest $request)
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
        $te = \Auth::user();
        $dealerLinkageRequest = DealerLinkageRequest::where([
            "id" => $request->linking_request_id
        ])->first();
        if(empty($dealerLinkageRequest))
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Invalid_linking_ID', $targetLanguage), 'data' => []]);
        $mason = User::find($dealerLinkageRequest->user_id);
        if($mason == null)
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Invalid_mason', $targetLanguage), 'data' => []]);
        if($mason->parent != $te->id)
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Invalid_linking_ID', $targetLanguage), 'data' => []]);
        if($dealerLinkageRequest->status != 0)
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Action_has_been_already_taken', $targetLanguage), 'data' => []]);
        try {
            DB::beginTransaction();
                // $masonDealers = $user->dealer_ids;
                $linkedDealerIds = MasonDealer::where("mason_id", $mason->id)->pluck("dealer_id")->toArray();
                if(count($linkedDealerIds) == 0)
                {
                    $mason->update([
                        "dealer_ids" => json_encode($dealerLinkageRequest->dealer_id)
                    ]);
                    MasonDealer::create([
                        "mason_id" => $mason->id,
                        "dealer_id" => $dealerLinkageRequest->dealer_id,
                    ]);
                }
                else
                {
                    if($mason->dealer_ids != null)
                    {
                        if(!is_array(json_decode($mason->dealer_ids)))
                        {
                            $mason->update([
                                "dealer_ids" => json_encode([json_decode($mason->dealer_ids)])
                            ]);
                        }
                        if(!in_array($dealerLinkageRequest->dealer_id, json_decode($mason->dealer_ids)))
                        {
                            $masonDealerIds = json_decode($mason->dealer_ids);
                            array_push($masonDealerIds, $dealerLinkageRequest->dealer_id);
                            $mason->update([
                                "dealer_ids" => $masonDealerIds
                            ]);
                        }
                    }
                    else
                    {
                        $mason->update([
                            "dealer_ids" => json_encode([$dealerLinkageRequest->dealer_id])
                        ]);
                    }
                    $linkedDealerIds = MasonDealer::where([
                        "mason_id" => $dealerLinkageRequest->user_id,
                        "dealer_id" => $dealerLinkageRequest->dealer_id,
                    ])->pluck("dealer_id")->toArray();
                    if(count($linkedDealerIds) == 0)
                    {
                        MasonDealer::create([
                            "mason_id" => $mason->id,
                            "dealer_id" => $dealerLinkageRequest->dealer_id,
                        ]);
                    }
                }
                $dealerLinkageRequest->update([
                    "status" => 1,
                    "action_taken_by" => $te->id,
                ]);
                DealerLinkageRequestHistory::create([
                    "dealer_linkage_request_id" => $dealerLinkageRequest->id,
                    "user_id" => $mason->id,
                    "dealer_id" => $dealerLinkageRequest->dealer_id,
                    "status" => 1,
                    "action_taken_by" => $te->id,
                ]);
                //Send Notification to Dealer
                $dealer = User::find($dealerLinkageRequest->dealer_id);
                $msg = "Mason ".$mason->name." Phone Number ".$mason->phone." is now tagged with you.";
                $notificationData = [
                    "notification_type" => "Dealer Linking Request",
                    "data" => [
                        "msg" => $msg,
                    ]
                ];
                Notification::send($dealer, new StarLinkNotification($notificationData));
                //Send SMS to Dealer
                $receiverNumber = $dealer->phone;
                // Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$receiverNumber.'&from=STARCM&text='.$msg.'&dlr-mask=19&dlr-url');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($e->getMessage(), $targetLanguage), 'data' => []]);
        }
        return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Linking_request_accepted_successfully', $targetLanguage), 'data' => []]);
    }
    public function rejectDealerLinkingRequest(RejectDealerLinkingRequest $request)
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
        $te = \Auth::user();
        $dealerLinkageRequest = DealerLinkageRequest::where([
            "id" => $request->linking_request_id
        ])->first();
        if(empty($dealerLinkageRequest))
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Invalid_linking_ID', $targetLanguage), 'data' => []]);
        $mason = User::find($dealerLinkageRequest->user_id);
        if($mason == null)
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Invalid_mason', $targetLanguage), 'data' => []]);
        if($mason->parent != $te->id)
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Invalid_linking_ID', $targetLanguage), 'data' => []]);
        if($dealerLinkageRequest->status != 0)
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Action_has_been_already_taken', $targetLanguage), 'data' => []]);
        try {
            DB::beginTransaction();
                //removing from MasonDealer
                $linkedDealerId = MasonDealer::where([
                    "mason_id" => $dealerLinkageRequest->user_id,
                    "dealer_id" => $dealerLinkageRequest->dealer_id,
                ])->first();
                if($linkedDealerId != null)
                {
                    $linkedDealerId->delete();
                }
                //removing from User
                if($mason->dealer_ids != null)
                {
                    if(!is_array(json_decode($mason->dealer_ids)))
                    {
                        $mason->update([
                            "dealer_ids" => json_encode([json_decode($mason->dealer_ids)])
                        ]);
                    }
                }
                if($mason->dealer_ids != null && in_array($dealerLinkageRequest->dealer_id, json_decode($mason->dealer_ids)))
                {
                    $diffDealers = array_diff(json_decode($mason->dealer_ids), [$dealerLinkageRequest->dealer_id]);
                    $updatedMasonDealers = [];
                    foreach($diffDealers as $diffDealer)
                    {
                        array_push($updatedMasonDealers, $diffDealer);
                    }
                    $mason->update([
                        "dealer_ids" => json_encode($updatedMasonDealers),
                    ]);
                }
                //updateding DealerLinkageRequest
                $dealerLinkageRequest->update([
                    "status" => 2,
                    "action_taken_by" => $te->id,
                ]);
                DealerLinkageRequestHistory::create([
                    "dealer_linkage_request_id" => $dealerLinkageRequest->id,
                    "user_id" => $mason->id,
                    "dealer_id" => $dealerLinkageRequest->dealer_id,
                    "status" => 2,
                    "action_taken_by" => $te->id,
                ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($e->getMessage(), $targetLanguage), 'data' => []]);
        }

        return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Linking_request_rejected_successfully', $targetLanguage), 'data' => []]);
    }
    // Dealer Linking Actions

    //third party access
    public function getLiftingEnquiries(Request $request)
    {
        try
        {
            $errorCode = 400;
            if(!$request->has('authkey') || empty($request->authkey))
            {
                throw new \Exception("Auth key is required");
            }
            if(!$request->has('sapcode') || empty($request->sapcode))
            {
                throw new \Exception('SAP Code is required');
            }
            $authkey = Setting::where('setting_name', 'star_sathi_auth_key')->pluck('setting_value')->first();
            if(empty($authkey))
            {
                $errorCode = 404;
                throw new \Exception('Auth Key is Not Found in the System.');
            }
            if($authkey != $request->authkey)
            {
                $errorCode = 401;
                throw new \Exception('Invalid Auth Key');
            }
            $dealer = User::where('sap_code',$request->sapcode)->first();
            if(empty($dealer))
            {
                $errorCode = 401;
                throw new \Exception('Invalid SAP Code');
            }
            if(!in_array($dealer->role, [User::DEALER]))
            {
                $errorCode = 403;
                $request->session()->put('error', 'Permission Denied, only Dealer has permission.');
            }
            $liftingEnquiries = LiftingEnquiry::select([
                'id',
                'enquiry_by',
                'enquiry_to',
                'product_id',
                'quantity',
                \DB::raw("DATE_FORMAT(date_of_lifting, '%Y-%m-%d') as date_of_lifting"),
                'lifting_query'
            ])->where("enquiry_to", $dealer->id)->get();
            if($liftingEnquiries->isEmpty())
            {
                return response()->json(['status'=> false, 'data' => [], 'msg' => "No data found"], 404);
            }
            return response()->json(['status'=> true, 'data' => $liftingEnquiries, 'msg' => "Lifting enquiries data fetched successfully"], 200);
        }
        catch(Exception $e)
        {
            return response()->json(['status'=> false, 'data' => [], 'msg' => $e->getMessage()], $errorCode ?? 400);
        }
    }
    //third party access
}