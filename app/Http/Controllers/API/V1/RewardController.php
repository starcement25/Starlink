<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Support;
use App\Models\Catalogue;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Mail;
use App\Models\UserCatalogueRedeemtion;
use App\Services\GoogleTranslateService;
use \Carbon\Carbon;

class RewardController extends Controller
{
    use HelperTrait;
    protected $googleTranslate;

    public function __construct(GoogleTranslateService $googleTranslate)
    {
        $this->googleTranslate = $googleTranslate;
    }
   
    function getRewards(Request $request)
    {   
        $rewards = DB::table('rewards as R')           
        ->join('users as U','U.id','=','R.user_id')
        ->leftJoin('lifting','R.lifting_id','=','lifting.id');
            if($request->user()->role == 2) {
               $rewards = $rewards->where('R.user_id',$request->user()->id);         
            }
            $rewards =  $rewards->select('R.id','R.point','R.bag','R.show_point','R.is_verified','U.name as mason_name','U.aadhaar_no as mason_aadhaar_no','U.phone as mason_phone','R.created_at as reward_date','R.description')
            ->where('R.is_verified',1)
            ->orderByDesc('R.id')                   
            ->get();
            if($rewards->isEmpty()) {
                return response()->json(['status'=> false,'msg' => "no reward awarded",'get_reward' => true, 'data' => []], 200);
            }
            try{
                $targetLanguage = \Auth::user()->preferred_app_lang;  // Default to English
                // return $this->googleTranslate->translateText("Subhajit", $targetLanguage);
                if(!empty(\Auth::user()->preferred_app_lang))
                {
                    foreach($rewards as $key => $val)
                    {
                        // $rewards[$key]->mason_name = $this->googleTranslate->translateText($rewards[$key]->mason_name, $targetLanguage);
                        $rewards[$key]->mason_name = $rewards[$key]->mason_name;
                        $rewards[$key]->description = $this->googleTranslate->translateText($rewards[$key]->description, $targetLanguage);
                    }
                }
            }
            catch(\Exception $e)
            {
                \Log::error($e->getMessage());
            }
            return response()->json(['status'=> true,'msg' => "rewards get successfully", 'get_reward' => true, 'data' => $rewards], 200);
    }

  function getRewardsByMason(Request $request)
    {   
        $input = $request->all();
        $rules = array(
                    'user_id' => 'required'
                );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if ($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $validRes['msg']]);
        } 
        $role = $request->user()->role; 
        $id   = $request->user_id;
        $mason = User::where('id',$id)->first();
        $netPoint = $mason->points ?? 0;
        // $rewards = DB::table('rewards as R')           
        // ->join('users as U','U.id','=','R.user_id')
        // ->leftJoin('lifting','R.lifting_id','=','lifting.id');
        //     $rewards = $rewards->where('R.user_id',$request->user_id);       
        //     $rewards =  $rewards->select('R.id','R.point','R.bag','R.show_point','R.is_verified',
        //     'U.name as mason_name','U.aadhaar_no as mason_aadhaar_no','U.phone as mason_phone','R.created_at as reward_date','R.description')
        //     ->where('R.is_verified',1)
        //     ->orderByDesc('R.id')                   
        //     ->get();

        if(\Auth::user()->preferred_app_lang != null)
        {
            //Pagination
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
            //end of pagination
            $rewards = DB::select("SELECT * FROM (
                SELECT IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at` AS `reward_date` 
                FROM `user_catalogue_redeemtions` WHERE `user_id`= ?  
                UNION ALL      
                SELECT'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at` AS `reward_date`
                FROM `rewards` WHERE `user_id`=?
                AND `is_verified`='1'
                UNION ALL      
                SELECT '',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at` AS `reward_date`
                FROM `rejected_redeemtions` WHERE `user_id`=?
                )P 
                ORDER BY `reward_date` DESC LIMIT ? OFFSET ?", [$id, $id, $id, $limit, $fetchDataFrom]);
            if(count($rewards) == 0)
            {
                return response()->json(['status' => false, 'msg' => 'No Data Found.', 'data' => []]);
            }
        }
        else
        {
            $rewards = DB::select("SELECT * FROM (
                SELECT IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at` AS `reward_date` 
                FROM `user_catalogue_redeemtions` WHERE `user_id`= ?  
                UNION ALL      
                SELECT'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at` AS `reward_date`
                FROM `rewards` WHERE `user_id`=?
                AND `is_verified`='1'
                UNION ALL      
                SELECT '',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at` AS `reward_date`
                FROM `rejected_redeemtions` WHERE `user_id`=?
                )P 
                ORDER BY `reward_date` DESC", [$id, $id, $id]);
        }
            // $registrationData[] = [
            //     "order_id"=> "",
            //     "user_id" => $id,
            //     "credit_point" => $mason->registration_point,
            //     "debit_point" => "",
            //     "description" => " Registration Bonus ",
            //     "reward_date" => date('Y-m-d H:i:s', strtotime($mason->created_at))
            // ];
            // $result = array_merge($registrationData, $rewards);
            if(count($rewards) == 0) {
                return response()->json(['status'=> false,'msg' => "no reward awarded", 'get_reward' => true, 'data' => []], 200);
            }
            try{
                $targetLanguage = \Auth::user()->preferred_app_lang;  // Default to English
                // return $this->googleTranslate->translateText("Subhajit", $targetLanguage);
                if(!empty(\Auth::user()->preferred_app_lang))
                {
                    foreach($rewards as $key => $val)
                    {
                        if(!empty($rewards[$key]->description))
                        {
                            $rewards[$key]->description = $this->googleTranslate->translateText($rewards[$key]->description, $targetLanguage);
                        }
                    }
                }
            }
            catch(\Exception $e)
            {
                \Log::error($e->getMessage());
            }
            return response()->json(['status'=> true,'msg' => "rewards get successfully", 'get_reward' => true,'net_point' =>$netPoint,  'data' => $rewards], 200);
    }



  function getSupport(Request $request)
    {   
        $input = $request->all();
        $rules = array(
                    'id' => 'required',
                     'user_id' => 'required',
                    'order_id' => 'required'        
                );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if ($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $validRes['msg']]);
        } 
        $role = $request->user()->role; 
        $id   = $request->user_id;
            $rewards = DB::table('supports as S')
            ->join('user_catalogue_redeemtions as UCR','UCR.id','=','S.order_id')
            ->join('users as U','U.id','=','UCR.user_id');
            $rewards = $rewards->where('UCR.user_id',$request->user_id)->where('UCR.order_id',$request->order_id);        
            $rewards =  $rewards->select('S.id','S.order_id','S.comment','S.support_type','S.image_path','S.status','U.name as mason_name','U.aadhaar_no as mason_aadhaar_no','U.phone as mason_phone')
            ->orderByDesc('S.id')                   
            ->get();
            if($rewards->isEmpty()) {
                return response()->json(['status'=> false,'msg' => "no data", 'get_reward' => true, 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'get_reward' => true, 'data' => $rewards], 200);
    }


  function saveSupport(Request $request)
  {   
        $input = $request->all();
        $rules = array(
                    'user_id' => 'required',
                     'order_id' => 'required',
                      'id' => 'required',
                     'comment'=>'required',
                     'support_type'=>'required',
        
                );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if ($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $validRes['msg']]);
        } 
        $role = $request->user()->role; 
        $id   = $request->user_id;

       
        $isDatas = Support::where('order_id', $request->order_id)->first();
         $image=null;
         
			 if($request->file('image_path'))
			 {
			 $files=$request->file('image_path');
			 $imageName = time();
			 $imageExt=$files->getClientOriginalName();
			 $image = $request->order_id.'.jpg';
			 $files->move(public_path('support'), $image);
			 } 
         
        if(!$isDatas)
        {
        
          
            $datas = Support::create([
                'order_id'  => $request->id, 
                'comment'  => $request->comment, 
                'support_type'         => $request->support_type,
                'status'         => 1,
                'image_path'         => $image
                ]) ;
    
                if(empty($datas)) {
                    return response()->json(['status'=> false,'msg' => "fails ", 'data' => []], 200);
                }
                return response()->json(['status'=> true,'msg' => " Saved Successfully", 'data' => $datas], 200);
        }else
        {
           
        
           $datas = Support::find($isDatas->id);           
            $datas->comment = $request->comment;    
			$datas->support_type = $request->support_type;	
            $datas->image_path = $image;
			$datas->save();
        
         if(empty($datas)) {
                    return response()->json(['status'=> false,'msg' => "fails ", 'data' => []], 200);
                }
                return response()->json(['status'=> true,'msg' => " Updated Successfully", 'data' => $datas], 200);
        
        
        }
  }

  function saveOrderFeedback(Request $request, $order_id)
  {
        try{
            $request->validate([
                "feedback" => "required|string"
            ]
            );
            $order = UserCatalogueRedeemtion::where("order_id", $order_id)->where("user_id", \Auth::user()->id)->first();
            if(empty($order))
            {
                return response()->json(['status' => false, 'msg' => "Invalid Order ID."]);
            }
            if(!in_array($order->status,[UserCatalogueRedeemtion::STATUS_DELIVERY_ACKNOWLEDGEMENT]))
            {
                return response()->json(['status' => false, 'msg' => "Feedback can be given after delivery acknowledge only."]);
            }
            // $orderFeedBackWindow = $this->settingVal("setting_name", "order_feedback_window");
            // if(!empty($orderFeedBackWindow) && $orderFeedBackWindow > 0 && !empty($order->delivery_confirmation_datetime))
            // {
            //     $feedbackWindowTime = Carbon::parse($order->delivery_confirmation_datetime)->copy()->addDays($orderFeedBackWindow);
            //     if(Carbon::now()->greaterThan($feedbackWindowTime))
            //     {
            //         return response()->json(['status' => false, 'msg' => "Feedback can be given within ".$orderFeedBackWindow." days of acknowledgement only."]);
            //     }
            // }
            $orderAcknowledgementWindow = $this->settingVal("setting_name", "order_acknowledgement_window");
            if(!empty($orderAcknowledgementWindow) && $orderAcknowledgementWindow > 0 && !empty($order->delivery_date))
            {
                $acknowledgementWindowTime = Carbon::parse($order->delivery_date)->copy()->addDays($orderAcknowledgementWindow);
                if(Carbon::now()->greaterThan($acknowledgementWindowTime))
                {
                    return response()->json(['status' => false, 'msg' => "Feedback can be given within ".$orderAcknowledgementWindow." days of Delivery only."]);
                }
            }
            $order->update([
                "feedback" => $request->feedback,
                "status" => UserCatalogueRedeemtion::STATUS_COMPLAINT_FEEDBACK,
            ]);

            $catelogue = Catalogue::find($order->catalogue_id);

            $feedbackEmail = $this->settingVal("setting_name", "feedback_email");
            $data['email'] = $feedbackEmail ;
            $data['subject'] = 'Product Feedback' ;
            $data['user'] = \Auth::user()?->name; "There is a feedback email from <b>".\Auth::user()?->name."</b> regarding the product " ;
            $data['product'] = $catelogue?->name; 
            $data['feedback'] = $request->input('feedback'); 
            $data['orderId'] = $order->order_id; 
            if(!empty($feedbackEmail)){

                //  [As Per Client Requirement Mail Part Is Commented Out Now 07/01/26].
                
                // Mail::send('emails.template', $data, function ($message) use ($data) {
                // $message->to($data['email'])
                //         ->subject($data['subject']);

                // });
            }
            return response()->json(['status' => true, 'msg' => "Feedback is Submitted Successfully."]);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
  }

  function checkUserLastOrderAcknowledgement()
  {
        $orderAcknowledgementWindow = $this->settingVal("setting_name", "order_acknowledgement_window");
        $orderAcknowledgementApplicableDateTime = $this->settingVal("setting_name", "order_acknowledgement_applicable_date_time");
        if(!empty($orderAcknowledgementWindow) && $orderAcknowledgementWindow > 0 && !empty($orderAcknowledgementApplicableDateTime))
        {
            $userUnacknowledgedOrders = UserCatalogueRedeemtion::where("user_id", \Auth::user()->id)
            ->where("status", UserCatalogueRedeemtion::STATUS_DELIVERED)
            //->where("is_delivery_confirmed", UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_NO)
            ->whereNull("is_delivery_confirmed")
            ->where("created_at", ">=", $orderAcknowledgementApplicableDateTime)->orderBy("id", "DESC")->get();
            
            // return $userUnacknowledgedOrders ;
            
            foreach($userUnacknowledgedOrders as $userUnacknowledgedOrder)
            {
                
                return response()->json(['status' => true, 'data' => "Pending acknowledgement of order found.", "order_details" => $userUnacknowledgedOrder]);
            }

          //  return "Hi";
        }

        return response()->json(['status' => false, 'data' => "No pending acknowledgement of order found.", "order_details" => null]);
    }


}

