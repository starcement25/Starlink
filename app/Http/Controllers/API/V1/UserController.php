<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\MasonDealer;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StaticPage;
use App\Models\ContactPage;
use App\Models\Faq;
use App\Models\Catalogue;
use App\Models\AppBanner;
use App\Models\MasonCategory;
use App\Models\SocialLink;
use App\Traits\HelperTrait;
use App\Models\UserCatalogueRedeemtion;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleTranslateService;
use App\Utils\LocalLanguageTranslation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
	use HelperTrait;

    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }

    function myProfile(Request $request)
    {
        $user = getProfile($request->user()->id);
        return response()->json(['status'=> false, 'data' => $user, 'msg' => "Please Update your Application."], 200);
    }
    function userProfile(Request $request)
    {
        $user = getProfile($request->user()->id);
        if(!empty($user["preferred_app_lang"]))
        {
            $targetLanguage = $user["preferred_app_lang"];
            $user["country"] = $this->googleTranslate->translateText($user["country"], $targetLanguage);
            $user["city"] = $this->googleTranslate->translateText($user["city"], $targetLanguage);
            // $user["name"] = $this->googleTranslate->translateText($user["name"], $targetLanguage);
            $user["name"] = $user["name"];
            $user["role_name"] = $this->googleTranslate->translateText($user["role_name"], $targetLanguage);
            // $user["mason_category"]['name'] = $this->googleTranslate->translateText($user["mason_category"]['name'], $targetLanguage);
            $user["mason_category"]['name'] = $user["mason_category"]['name'];
            $user["profile_pic"] = $user["profile_pic"]."?t=".time();
        }
        $unreadNotificationCount = Notification::where([
            "notifiable_id" => $user["id"],
            "read_at" => null
        ])->count();
        $user["unreadNotificationCount"] = $unreadNotificationCount;
        return response()->json(['status'=> true, 'data' => $user, 'msg' => "User data fetch successfully"], 200);
    }
    function updateProfile(Request $request)
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
            $input = $request->all();
            $rules = array(
                        'phone' => 'required|min:10|max:10',
                        'email' => 'required|email',
                        'name' => 'required',
                    );
            $validator  = Validator::make($input, $rules);
            $validRes = validateInput($validator);
            if ($validRes['status'] == false) {
                return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($validRes['msg'], $targetLanguage)]);
            }
            $user_id = $request->user()->id;
            $data = $validRes['validated_data'];
            if($request->aadhaar_no) {
                $data['aadhaar_no'] = $request->aadhaar_no;
            }
            User::where('id',$user_id)->update($data);
            DB::commit();
            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Updated_successfully', $targetLanguage)]);   
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Someting_went_wrong", $targetLanguage)]);
        }
    }
    function updateUserPreferences(Request $request)
    {
        try {
            $data = [];
            if($request->has('preferred_app_lang'))
            {
                $request->validate([
                    "preferred_app_lang" => "required|string"
                ]);
                if(!in_array($request->preferred_app_lang, GoogleTranslateService::languageCodes()))
                {
                    throw new \Exception("Invalid Language Code.");
                }
                $data['preferred_app_lang'] = $request->preferred_app_lang;
            }
            if(count($data) > 0)
            {
                User::where('id',\Auth::user()->id)->update($data);
                DB::commit();
                return response()->json(['status' => true, 'msg' => 'Updated Successfully']);
            }
            return response()->json(['status' => true, 'msg' => 'No user preference found to update.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => "Someting went wrong : ".$e->getMessage()]);
        }
    }
    function changeProfilePic(Request $request)
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
                    'pic' => 'mimes:jpeg,jpg,png|required',
                );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if ($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($validRes['msg'], $targetLanguage)]);
        }
        $user_id = $request->user()->id;
        if($request->file('pic')) {
            $file = $request->file('pic');
            $filename = "M".$user_id.".".$request->file('pic')->getClientOriginalExtension();
            $location = base_path().'/public/pic';
            $file->move($location,$filename);
            $user = User::find($user_id);
            $picUrl = asset('/public/pic').'/'.$filename;
            $user->profile_pic = $picUrl;
            $user->save();
            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Profile_pic_updated_successfully', $targetLanguage), 'data' =>['new_pic' => $picUrl]]);
        }
    }


    function getAbout(Request $request)
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
        $datas =  StaticPage::where('page_slug','about-us')->first();
        if(empty($datas)) {
            return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage), 'data' => []], 200);
        }
        if(!empty($targetLanguage))
        {
            return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $this->googleTranslate->translateText($datas->value, $targetLanguage)], 200);
        }
        return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $datas], 200);
    }

    function getPrivacy(Request $request)
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
        $datas =  StaticPage::where('page_slug','privacy-policy')->first();
        if(empty($datas)) {
            return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage), 'data' => []], 200);
        }
        if(!empty($targetLanguage))
        {
            return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $this->googleTranslate->translateText($datas->value, $targetLanguage)], 200);
        }
        return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $datas], 200);
    }

    function getTerms(Request $request)
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
        $datas =  StaticPage::where('page_slug','terms-and-conditions')->first();
        if(empty($datas)) {
            return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage), 'data' => []], 200);
        }
        if(!empty($targetLanguage))
        {
            return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $this->googleTranslate->translateText($datas->value, $targetLanguage)], 200);
        }
        return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $datas], 200);
    }

    function getContact(Request $request)
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
        $datas =  ContactPage::first();
        if(empty($datas)) {
            return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage), 'data' => []], 200);
        }
        return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $datas], 200);
    }

    function getFAQ(Request $request)
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
        $datas =  Faq::where("status", Faq::STATUS_ACTIVE)->get();
        if(empty($datas)) {
            return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage), 'data' => []], 200);
        }
        if(!empty($targetLanguage))
        {
            foreach($datas as $key => $data)
            {
                $datas[$key]->question = $this->googleTranslate->translateText($data->question, $targetLanguage);
                $datas[$key]->answer = $this->googleTranslate->translateText($data->answer, $targetLanguage);
            }
        }
        return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $datas], 200);
    }

    function getGiftLink(Request $request)
    {   
        // $datas =  Catalogue::get();
        $datas =  MasonCategory::with(['catalogues' =>  function($q){
            $q->where('status',1);
        }])->orderBy('from_point')->get();
        // if(!empty(\Auth::user()->preferred_app_lang ?? null))
        // {
        //     $targetLanguage = (\Auth::user()->preferred_app_lang);
        //     foreach($datas as $key => $data)
        //     {
        //         $datas[$key]->name = $this->googleTranslate->translateText($data->name, $targetLanguage);
        //         foreach($data->catalogues as $catalogueKey => $catalogue)
        //         {
        //             $data->catalogues[$catalogueKey]->name = $this->googleTranslate->translateText($catalogue->name, $targetLanguage);
        //             $data->catalogues[$catalogueKey]->description = $this->googleTranslate->translateText($catalogue->description, $targetLanguage);
        //         }
        //     }
        // }
        if(empty($datas)) {
            return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
        }
        return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }
    function getGiftCatalogues(Request $request)
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
        if(!empty($targetLanguage))
        {
            // $page = 1;
            // if($request->has("page") && $request->page != null)
            // {
            //     $page = $request->page;
            // }
            // if($page < 1)
            // {
            //     return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 404);
            // }
            // $limit = 6;
            // $fetchDataFrom = $limit * ($page - 1);
            // $datas =  MasonCategory::skip($fetchDataFrom)->take($limit)->orderBy('from_point')->get();
            $datas =  MasonCategory::orderBy('from_point')->get();
            $targetLanguage = (\Auth::user()->preferred_app_lang);
            foreach($datas as $key => $data)
            {
                $datas[$key]->keyword = $data->name;
                // $datas[$key]->name = $this->googleTranslate->translateText($data->name, $targetLanguage);
                $datas[$key]->name = $data->name;
                if(\Auth::check() && \Auth::user()->role == User::TECHNICAL_ENGINEER)
                {
                    $masonCount = User::where("role", User::MASON)->whereBetween("points", [$data->from_point, $data->to_point])->count();
                    $datas[$key]->mason_count = $masonCount;
                    $datas[$key]->name = $data->name." \n(".$this->localLanguageTranslate->translate("Total_contractor", $targetLanguage)." : ".$masonCount.")";
                }
            }
        }
        else
        {
            $datas =  MasonCategory::orderBy('from_point')->get();
        }
        if($datas->isEmpty()) {
            return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage), 'data' => []], 200);
        }
        return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $datas], 200);
    }

    function getGiftsByCategory(Request $request, $categoryID)
    {
        try{
            $targetLanguage = null; 
            $tdsPercentage = $this->settingVal("setting_name", "catalogue_tds_percentage");
            
            if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
            {
                $targetLanguage = $request->preferred_app_lang;
            }
            if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
            {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $giftCategory = MasonCategory::find($categoryID);
            if(empty($giftCategory))
            {
                throw new \Exception($this->localLanguageTranslate->translate("Invalid_gift_category", $targetLanguage));
            }
            if(!empty($targetLanguage))
            {
                // $page = 1;
                // if($request->has("page") && $request->page != null)
                // {
                //     $page = $request->page;
                // }
                // if($page < 1)
                // {
                //     return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 404);
                // }
                // $limit = 6;
                // $fetchDataFrom = $limit * ($page - 1);
                $gifts = Catalogue::where([
                    "status" => Catalogue::STATUS_ACTIVE,
                    "mason_category_id" => $categoryID,
                ])->get();
                // $gifts = Catalogue::where([
                //     "status" => Catalogue::STATUS_ACTIVE
                // ])->skip($fetchDataFrom)->take($limit)->get();
                foreach($gifts as $key => $gift)
                {
                    $tdsPoint = round((($gift->point * $tdsPercentage) / 100), 2) ;

                    // $gifts[$key]->name = $this->googleTranslate->translateText($gift->name, $targetLanguage);
                    $gifts[$key]->name = $gift->name;
                    $gifts[$key]->description = $this->googleTranslate->translateText($gift->description, $targetLanguage);

                    $gifts[$key]->tds_percentage = $tdsPercentage;
                    $gifts[$key]->tds_point = $tdsPoint  ;
                    $gifts[$key]->total_point = ($tdsPoint + $gift->point ) ;
                }
            }
            else
            {
                $gifts = Catalogue::where([
                    "status" => Catalogue::STATUS_ACTIVE
                ])->get();
            }
            if($gifts->isEmpty())
            {
                // throw new \Exception($this->localLanguageTranslate->translate("No_gifts_found", $targetLanguage));
                throw new \Exception($this->localLanguageTranslate->translate("no_gifts_found_please_qualify_to_the_next_slab_for_rewards", $targetLanguage));
            }
            foreach($gifts as $gift)
            {
                $gift["is_email_required"] = $gift->catalogue_type_id == 2 ? true : false;
            }
            return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Gifts_got_successfully", $targetLanguage), 'data' => $gifts], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['status'=> false,'msg' => $e->getMessage(), 'data' => []], 404);
        }
    }

    function getSocialLink(Request $request)
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
        $datas =  SocialLink::get();
        if(empty($datas)) {
            return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage), 'data' => []], 200);
        }
        return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => $datas], 200);
    }

    function getNotification(Request $request)
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
         $user_id   = $request->user_id;
        $datas =  Notification::where('user_id', $user_id  )->get();
            if(empty($datas)) {
                return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }

    function getRedeemtion(Request $request)
    {   
        $role = $request->user()->role; 
             $id   = $request->user()->id;
    
            $rewards = DB::table('user_catalogue_redeemtions as UCR')
            ->LeftJoin('users as U','U.id','=','UCR.user_id');
            if($request->user()->role == 2) {
               $rewards = $rewards->where('UCR.user_id',$id);         
            }
            $rewards =  $rewards->select('UCR.id','UCR.redeemed_point as point','UCR.bag','UCR.show_point','UCR.is_verified','U.name as mason_name','U.aadhaar_no as mason_aadhaar_no','U.phone as mason_phone','UCR.created_at as reward_date','UCR.description')
            ->orderByDesc('UCR.id')                   
            ->get();
            if($rewards->isEmpty()) {
                return response()->json(['status'=> false,'msg' => "no data found", 'get_reward' => false, 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'get_reward' => false, 'data' => $rewards], 200);

    }

function getRedeemtionByMason(Request $request)
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
            $rewards = DB::table('user_catalogue_redeemtions as UCR')
            ->LeftJoin('users as U','U.id','=','UCR.user_id');
               $rewards = $rewards->where('UCR.user_id',$request->user_id); 
            $rewards =  $rewards->select('UCR.id','UCR.redeemed_point as point','UCR.bag','UCR.is_verified','U.name as mason_name','U.aadhaar_no as mason_aadhaar_no','U.phone as mason_phone','UCR.created_at as reward_date','UCR.description','UCR.show_point')
            ->orderByDesc('UCR.id')                   
            ->get();
            if($rewards->isEmpty()) {
                return response()->json(['status'=> false,'msg' => "no data found",'get_reward' => false, 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'get_reward' => false, 'data' => $rewards], 200);

    }


    // function applyRedeemtion_PREV(Request $request)
    // {   
    //     try
    //     {
    //         $targetLanguage = null;
    //         if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
    //         {
    //             $targetLanguage = $request->preferred_app_lang;
    //         }
    //         if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
    //         {
    //             $targetLanguage = \Auth::user()->preferred_app_lang;
    //         }
    //         $isRedeemServiceAvailable = $this->settingVal("setting_name", "app_redeem_now_button");
    //         if($isRedeemServiceAvailable != 1)
    //         {
    //             return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("redemption_is_down_work_is_under_progress_sorry_for_the_inconvenience_caused", $targetLanguage), 'data' => []], 200);
    //         }
    //         \DB::beginTransaction();
    //         $input = $request->all();
    //         $rules = array(
    //             'role' => 'required',
    //             'catalogue_id' => 'required',
    //             'user_id' => 'required',
    //             'redeemed_point' => 'required',
    //             'address1' => 'required',
    //             'address2' => 'required',
    //             'city' => 'required',
    //             'district' => 'required',
    //             'state' => 'required',
    //             'country' => 'required',
    //             'pincode' => 'required|digits:6'
    //         );
    //         $catalogue = Catalogue::lockForUpdate()->find($request->catalogue_id);
    //         if(empty($catalogue) || $catalogue->status == Catalogue::STATUS_DISABLE)
    //         {
    //             \DB::rollback();
    //             return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("Invalid_gift", $targetLanguage), 'data' => []], 200);
    //         }
    //         if($catalogue->catalogue_type_id == 2)
    //         {
    //             $rules["email"] = 'required|email:rfc,dns';
    //         }
    //         $validationMsg = [
    //             'role.required' => 'Redemption failed, role not found. Please contact admin.',
    //             'catalogue_id.required' => 'Redemption failed, catalogue found. Please contact admin.',
    //             'user_id.required' => 'Redemption failed, user found. Please contact admin.',
    //             'redeemed_point.required' => 'Redemption failed, point found. Please contact admin.',
    //             'email.required' => $this->localLanguageTranslate->translate('Redemption_failed,_email_is_required._Please_try_Again', $targetLanguage),
    //             'email.email' => $this->localLanguageTranslate->translate('Redemption_failed,_valid_email_is_required._Please_try_Again', $targetLanguage),
    //             'address1.required' => $this->localLanguageTranslate->translate('Redemption_failed,_address_1_is_required._Please_try_Again', $targetLanguage),
    //             'address2.required' => $this->localLanguageTranslate->translate('Redemption_failed,_address_2_is_required._Please_try_Again', $targetLanguage),
    //             'city.required' => $this->localLanguageTranslate->translate('Redemption_failed,_city_is_required._Please_try_Again', $targetLanguage),
    //             'district.required' => $this->localLanguageTranslate->translate('Redemption_failed,_district_is_required._Please_try_Again', $targetLanguage),
    //             'state.required' => $this->localLanguageTranslate->translate('Redemption_failed,_state_is_required._Please_try_Again', $targetLanguage),
    //             'country.required' => $this->localLanguageTranslate->translate('Redemption_failed,_country_is_required._Please_try_Again', $targetLanguage),
    //             'pincode.required' => $this->localLanguageTranslate->translate('Redemption_failed,_pincode_is_required._Please_try_Again', $targetLanguage),
    //         ];
    //         // validation 
    //         $validator  = Validator::make($input, $rules, $validationMsg);
    //         $res = validationFailer($validator);
    //         if ($res['status'] == false) {
    //             \DB::rollback();
    //             return response()->json(['status' => false,'msg' => $res['msg']]);
    //         }
    //         $customErrors = [
    //             'address1' => $this->localLanguageTranslate->translate('Redemption_failed,_address_1_is_required._Please_try_Again', $targetLanguage),
    //             'address2' => $this->localLanguageTranslate->translate('Redemption_failed,_address_2_is_required._Please_try_Again', $targetLanguage),
    //             'city' => $this->localLanguageTranslate->translate('Redemption_failed,_city_is_required._Please_try_Again', $targetLanguage),
    //             'district' => $this->localLanguageTranslate->translate('Redemption_failed,_district_is_required._Please_try_Again', $targetLanguage),
    //             'state' => $this->localLanguageTranslate->translate('Redemption_failed,_state_is_required._Please_try_Again', $targetLanguage),
    //             'country' => $this->localLanguageTranslate->translate('Redemption_failed,_country_is_required._Please_try_Again', $targetLanguage),
    //             'pincode' => $this->localLanguageTranslate->translate('Redemption_failed,_pincode_is_required._Please_try_Again', $targetLanguage),
    //         ];
    //         //becuase we have observed from app "null" input was coming 
    //         foreach($customErrors as $error => $errorMsg)
    //         {
    //             if($request->$error === "null")
    //             {
    //                 \DB::rollback();
    //                 return response()->json(['status' => false,'msg' => $errorMsg]);
    //             }
    //         }
            

    //         if($request->role == 2)
    //         {
    //             $masonNetPoint = User::lockForUpdate()->find($request->user_id)->points ?? null;
    //             if($masonNetPoint == null)
    //             {
    //                 \DB::rollback();
    //                 return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("Mason_not_found", $targetLanguage), 'data' => []], 200);
    //             }
    //             $catalogueTDSPercentage = $this->settingVal("setting_name", "catalogue_tds_percentage");
    //             $catalogue_tds_point = 0.00;
    //             if($catalogueTDSPercentage && $catalogueTDSPercentage > 0)
    //             {
    //                 $catalogue_tds_point = number_format($catalogue->point * ($catalogueTDSPercentage / 100), 2);
    //                 $catalogue_redeem_point = $catalogue->point + $catalogue_tds_point;
    //                 if($catalogue_redeem_point > $masonNetPoint)
    //                 {
    //                     \DB::rollback();
    //                     return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("Insufficient_points_Your_available_balance_is_lower_than_the_catalogue_value_including_TDS_amount", $targetLanguage), 'data' => []], 200);
    //                 }
    //             }
    //             else
    //             {
    //               //  return response()->json(['status'=> false,'msg' => "Hi"], 200);
    //                 $catalogue_redeem_point = $catalogue->point;
    //                 //Checking Mason have enough points to redeem or not
    //                 if($catalogue->point > $masonNetPoint)
    //                 {
    //                     \DB::rollback();
    //                     return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("You_don't_have_enough_points_to_redeem_this_gift", $targetLanguage), 'data' => []], 200);
    //                 }
    //             }

    //             $rewards = Reward::where('user_id', $request->user_id)
    //             ->where('is_verified', 1)
    //             ->lockForUpdate()
    //             ->get();

    //             // find the gift name from catalog id 
    //             // $catalogs = Catalogue::where('id',$request->catalogue_id)->first();
    //             //dd($catalogs);
    //             $gift_name = $catalogue->name;
    //             // for add redeemtion points
    //             $userCat =  new UserCatalogueRedeemtion;
    //             $userCat->user_id = $request->user_id;
    //             //$userCat->order_id = 'ORD'.rand(10000,99999);
    //             $userCat->catalogue_id = $request->catalogue_id;
    //             $userCat->catalogue_tds_percentage = $catalogueTDSPercentage;
    //             $userCat->catalogue_tds_point = $catalogue_tds_point;
    //             $userCat->catalogue_point = $catalogue->point; 
    //             $userCat->redeemed_point = $catalogue_redeem_point; 
    //             $userCat->email = $catalogue->catalogue_type_id == 2 ? $request->email : null;
    //             $userCat->address1 = $request->address1;
    //             $userCat->address2 = $request->address2;
    //             $userCat->city = $request->city;
    //             $userCat->district = $request->district;
    //             $userCat->state = $request->state;
    //             $userCat->country = $request->country;
    //             $userCat->pincode = $request->pincode;
    //             $userCat->description = "Gift Redeemed of gift : $gift_name";
            
    //             $userCat->save();
    //             $userCat->order_id = "ORD".str_pad($userCat->id,5,0,STR_PAD_LEFT);
    //             $userCat->save();
                
    //             // update the points of mason
    //             $this->updatePoint($request->user_id);
    //                 if(empty($userCat)) {
    //                     \DB::rollback();
    //                     return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []], 200);
    //                 }
    //                 \DB::commit();
    //                 return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Gift_redeemed_successfully", $targetLanguage), 'data' => $userCat], 200);
    //         }
    //         else
    //         {
    //             \DB::rollback();
    //             return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("You're_not_able_to_redeem_gift_Only_mason_can_able_to_redeem_the_gift", $targetLanguage), 'data' => []], 200);
    //         }
    //     }
    //     catch(\Exception $e)
    //     {
    //         \DB::rollback();
    //         return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("Redeemtion_service_is_not_available", $targetLanguage), 'data' => ["error_details" => $e->getMessage()]], 200);
    //     }
           
    // }

    function applyRedeemtion(Request $request)
    {   
        try
        {
            
           // return response()->json(['status'=> false,'msg' => 'Try Later', 'data' => []], 200);
            $targetLanguage = null;
            if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
            {
                $targetLanguage = $request->preferred_app_lang;
            }
            if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
            {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $isRedeemServiceAvailable = $this->settingVal("setting_name", "app_redeem_now_button");
            if($isRedeemServiceAvailable != 1)
            {
                return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("redemption_is_down_work_is_under_progress_sorry_for_the_inconvenience_caused", $targetLanguage), 'data' => []], 200);
            }
            \DB::beginTransaction();
            $input = $request->all();
            $rules = array(
                'role' => 'required',
                'catalogue_id' => 'required',
                'user_id' => 'required',
                'redeemed_point' => 'required',
                'address1' => 'required',
                'address2' => 'required',
                'city' => 'required',
                'district' => 'required',
                'state' => 'required',
                'country' => 'required',
                'pincode' => 'required|digits:6',
                'email' => 'required|email',
               
                
            );
            $catalogue = Catalogue::lockForUpdate()->find($request->catalogue_id);
            if(empty($catalogue) || $catalogue->status == Catalogue::STATUS_DISABLE)
            {
                \DB::rollback();
                return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("Invalid_gift", $targetLanguage), 'data' => []], 200);
            }
            // if($catalogue->catalogue_type_id == 2)
            // {
            //     $rules["email"] = 'required|email:rfc,dns';
            // }

            if($catalogue->catalogue_type_id == 1)
            {
                $rules["phone"] =  'required|digits:10';
            }

            $validationMsg = [
                'role.required' => 'Redemption failed, role not found. Please contact admin.',
                'catalogue_id.required' => 'Redemption failed, catalogue found. Please contact admin.',
                'user_id.required' => 'Redemption failed, user found. Please contact admin.',
                'redeemed_point.required' => 'Redemption failed, point found. Please contact admin.',
                'email.required' => $this->localLanguageTranslate->translate('Redemption_failed,_email_is_required._Please_try_Again', $targetLanguage),
                'email.email' => $this->localLanguageTranslate->translate('Redemption_failed,_valid_email_is_required._Please_try_Again', $targetLanguage),
                'address1.required' => $this->localLanguageTranslate->translate('Redemption_failed,_address_1_is_required._Please_try_Again', $targetLanguage),
                'address2.required' => $this->localLanguageTranslate->translate('Redemption_failed,_address_2_is_required._Please_try_Again', $targetLanguage),
                'city.required' => $this->localLanguageTranslate->translate('Redemption_failed,_city_is_required._Please_try_Again', $targetLanguage),
                'district.required' => $this->localLanguageTranslate->translate('Redemption_failed,_district_is_required._Please_try_Again', $targetLanguage),
                'state.required' => $this->localLanguageTranslate->translate('Redemption_failed,_state_is_required._Please_try_Again', $targetLanguage),
                'country.required' => $this->localLanguageTranslate->translate('Redemption_failed,_country_is_required._Please_try_Again', $targetLanguage),
                'pincode.required' => $this->localLanguageTranslate->translate('Redemption_failed,_pincode_is_required._Please_try_Again', $targetLanguage),
            ];
            // validation 
            $validator  = Validator::make($input, $rules, $validationMsg);
            $res = validationFailer($validator);
            if ($res['status'] == false) {
                \DB::rollback();
                return response()->json(['status' => false,'msg' => $res['msg']]);
            }
            $customErrors = [
                'address1' => $this->localLanguageTranslate->translate('Redemption_failed,_address_1_is_required._Please_try_Again', $targetLanguage),
                'address2' => $this->localLanguageTranslate->translate('Redemption_failed,_address_2_is_required._Please_try_Again', $targetLanguage),
                'city' => $this->localLanguageTranslate->translate('Redemption_failed,_city_is_required._Please_try_Again', $targetLanguage),
                'district' => $this->localLanguageTranslate->translate('Redemption_failed,_district_is_required._Please_try_Again', $targetLanguage),
                'state' => $this->localLanguageTranslate->translate('Redemption_failed,_state_is_required._Please_try_Again', $targetLanguage),
                'country' => $this->localLanguageTranslate->translate('Redemption_failed,_country_is_required._Please_try_Again', $targetLanguage),
                'pincode' => $this->localLanguageTranslate->translate('Redemption_failed,_pincode_is_required._Please_try_Again', $targetLanguage),
            ];
            //becuase we have observed from app "null" input was coming 
            foreach($customErrors as $error => $errorMsg)
            {
                if($request->$error === "null")
                {
                    \DB::rollback();
                    return response()->json(['status' => false,'msg' => $errorMsg]);
                }
            }
            

            if($request->role == 2)
            {
                $masonNetPoint = User::lockForUpdate()->find($request->user_id)->points ?? null;
                if($masonNetPoint == null)
                {
                    \DB::rollback();
                    return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("Mason_not_found", $targetLanguage), 'data' => []], 200);
                }

                $catalogueTDSPercentage = $this->settingVal("setting_name", "catalogue_tds_percentage");
                $catalogue_tds_point = 0.00;
                if($catalogueTDSPercentage && $catalogueTDSPercentage > 0)
                {
                    $catalogue_tds_point = round(($catalogue->point * ($catalogueTDSPercentage / 100)), 2);
                    $catalogue_redeem_point = $catalogue->point + $catalogue_tds_point;
                    if($catalogue_redeem_point > $masonNetPoint)
                    {
                        \DB::rollback();
                        return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("Insufficient_points_Your_available_balance_is_lower_than_the_catalogue_value_including_TDS_amount", $targetLanguage), 'data' => []], 200);
                    }
                }
                else
                {
                    $catalogue_redeem_point = $catalogue->point;
                    //Checking Mason have enough points to redeem or not
                    if($catalogue->point > $masonNetPoint)
                    {
                        \DB::rollback();
                        return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("You_don't_have_enough_points_to_redeem_this_gift", $targetLanguage), 'data' => []], 200);
                    }
                }

                $orderAcknowledgementWindow = $this->settingVal("setting_name", "order_acknowledgement_window");
                $orderAcknowledgementApplicableDateTime = $this->settingVal("setting_name", "order_acknowledgement_applicable_date_time");
                
                // Checking Acknowledgement Provided Or Not.
                if(!empty($orderAcknowledgementWindow) && $orderAcknowledgementWindow > 0 && !empty($orderAcknowledgementApplicableDateTime))
                {
                    $userUnacknowledgedOrders = UserCatalogueRedeemtion::where("user_id", \Auth::user()->id)
                    ->where("status", UserCatalogueRedeemtion::STATUS_DELIVERED)
                    //->where("is_delivery_confirmed", UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_NO)
                    ->whereNull("is_delivery_confirmed")
                    ->where("created_at", ">=", $orderAcknowledgementApplicableDateTime)
                    ->get();

                    // If Not Then can't Order Further.
                    foreach($userUnacknowledgedOrders as $userUnacknowledgedOrder)
                    {
                        return response()->json(['status'=> false,'msg' => "You cannot order as Order with Order ID ".$userUnacknowledgedOrder->order_id." is not acknowledged yet.", 'data' => []], 200);
                    }
                }

                $rewards = Reward::where('user_id', $request->user_id)
                ->where('is_verified', 1)
                ->lockForUpdate()
                ->get();

                // find the gift name from catalog id 
                // $catalogs = Catalogue::where('id',$request->catalogue_id)->first();
                //dd($catalogs);
                $gift_name = $catalogue->name;
                // for add redeemtion points
                $userCat =  new UserCatalogueRedeemtion;
                $userCat->user_id = $request->user_id;
                //$userCat->order_id = 'ORD'.rand(10000,99999);
                $userCat->catalogue_id = $request->catalogue_id;
                // $userCat->redeemed_point = $catalogue->point; 
                $userCat->catalogue_tds_percentage = $catalogueTDSPercentage;
                $userCat->catalogue_tds_point = $catalogue_tds_point;
                $userCat->catalogue_point = $catalogue->point; 
                $userCat->redeemed_point = $catalogue_redeem_point;
                // $userCat->email = $catalogue->catalogue_type_id == 2 ? ($request->email) : null;
                $userCat->email = $request->email ?? null;
                $userCat->phone = $catalogue->catalogue_type_id == 1 ? ($request->phone) : null;
                $userCat->address1 = $request->address1;
                $userCat->address2 = $request->address2;
                $userCat->city = $request->city;
                $userCat->district = $request->district;
                $userCat->state = $request->state;
                $userCat->country = $request->country;
                $userCat->pincode = $request->pincode;
                $userCat->description = "Gift Redeemed of gift : $gift_name";
            
                $userCat->save();
                // $userCat->order_id = "ORD".str_pad($userCat->id,5,0,STR_PAD_LEFT);
                $userCat->order_id = $this->createRedemptionOrderID($userCat->id);
                $userCat->save();
                
                // update the points of mason
                $this->updatePoint($request->user_id);
                    if(empty($userCat)) {
                        \DB::rollback();
                        return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []], 200);
                    }
                    \DB::commit();
                    return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Gift_redeemed_successfully", $targetLanguage), 'data' => $userCat], 200);
            }
            else
            {
                \DB::rollback();
                return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("You're_not_able_to_redeem_gift_Only_mason_can_able_to_redeem_the_gift", $targetLanguage), 'data' => []], 200);
            }
        }
        catch(\Exception $e)
        {
           //\Log::info("Incoming Request".request()->post());
            \Log::info("ORDER FOR USER ".\Auth::user()->id." ".$e->getMessage());
            \DB::rollback();
            return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("You'redeemtion_service_is_not_available", $targetLanguage), 'data' => ["error_details" => $e->getMessage()]], 200);
        }
           
    }




 function viewBanner(Request $request)
 {    
    //$x = $this->getLiftingCurrMonthMason(7,139,148);
        //dd($x);
        $datas = AppBanner::where('status', 1)->get();        
    if($datas->isEmpty())
    return response()->json(['status' => false, 'msg' => 'No data found', 'data' => $datas]);  
    else
    return response()->json(['status' => true, 'msg' => 'Data get successfully', 'data' => $datas]); 
 }


//  function getOrderByMason_PREV(Request $request)
//  {   
//         $targetLanguage = null;
//         if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
//         {
//             $targetLanguage = $request->preferred_app_lang;
//         }
//         if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
//         {
//             $targetLanguage = \Auth::user()->preferred_app_lang;
//         }
//         $input = $request->all();
//         $rules = array(
//                     'user_id' => 'required'
//                 );
//         $validator  = Validator::make($input, $rules);
//         $validRes = validateInput($validator);
//         if ($validRes['status'] == false) {
//             return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($validRes['msg'], $targetLanguage)]);
//         } 
//         $role = $request->user()->role; 
//         $id   = $request->user_id;
//         if(!empty($targetLanguage))
//         {
//             $page = 1;
//             if($request->has("page") && $request->page != null)
//             {
//                 $page = $request->page;
//             }
//             if($page < 1)
//             {
//                 return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 404);
//             }
//             $limit = 6;
//             $fetchDataFrom = $limit * ($page - 1);
//             $rewards = DB::table('user_catalogue_redeemtions as UCR')
//             ->LeftJoin('users as U','U.id','=','UCR.user_id');
//                $rewards = $rewards->where('UCR.user_id',$request->user_id); 
//             $rewards =  $rewards->select('UCR.id','UCR.order_id','UCR.redeemed_point as point',
//             'UCR.bag','UCR.is_verified','U.name as mason_name',
//             'U.aadhaar_no as mason_aadhaar_no',
//             'U.phone as mason_phone','UCR.created_at as reward_date',
//             'UCR.description','UCR.show_point',
//             'UCR.status as delivery_status',
//             'UCR.is_delivery_confirmed',
//             'UCR.delivery_confirmation_datetime',
//             'UCR.comment','UCR.delivery_date')
//             ->whereNotNull('UCR.order_id')
//             ->orderByDesc('UCR.id') 
//             ->skip($fetchDataFrom)->take($limit)                  
//             ->get();
//             foreach($rewards as $key => $rewardVal)
//             {
//                 // $rewards[$key]->mason_name = $this->googleTranslate->translateText($rewardVal->mason_name, $targetLanguage);
//                 $rewards[$key]->mason_name = $rewardVal->mason_name;
//                 $rewards[$key]->isConfirmEnabled = false;
//                 $rewards[$key]->description = $this->googleTranslate->translateText($rewardVal->description, $targetLanguage);
//                 $deliveryStatusValue = "Pending";
//                 if($rewardVal->delivery_status == 1)
//                 {
//                     $deliveryStatusValue = "Delivered";
//                     if($rewards[$key]->is_delivery_confirmed != UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_YES)
//                     {
//                         $rewards[$key]->isConfirmEnabled = true;
//                     }
//                 }
//                 else if($rewardVal->delivery_status == 2)
//                 {
//                     $deliveryStatusValue = "Rejected";
//                 }
//                 else if($rewardVal->delivery_status == 3)
//                 {
//                     $deliveryStatusValue = "Order Placed";
//                 }
//                 $rewards[$key]->delivery_status_value = $this->localLanguageTranslate->translate($deliveryStatusValue, $targetLanguage);
//                 $rewards[$key]->delivery_confirmed_at = $rewardVal->delivery_confirmation_datetime?->toDateTimeString();
//             }
//         }
//         else
//         {
//             $rewards = DB::table('user_catalogue_redeemtions as UCR')
//             ->LeftJoin('users as U','U.id','=','UCR.user_id');
//                $rewards = $rewards->where('UCR.user_id',$request->user_id); 
//             $rewards =  $rewards->select('UCR.id','UCR.order_id','UCR.redeemed_point as point',
//             'UCR.bag','UCR.is_verified','U.name as mason_name',
//             'U.aadhaar_no as mason_aadhaar_no',
//             'U.phone as mason_phone','UCR.created_at as reward_date',
//             'UCR.description','UCR.show_point',
//             'UCR.status as delivery_status',
//             'UCR.comment','UCR.delivery_date',
//             DB::raw('CASE 
//                     WHEN UCR.status = 0 THEN "Pending"
//                     WHEN UCR.status = 1 THEN "Delivered"
//                     WHEN UCR.status = 2 THEN "Delivered"
//                     WHEN UCR.status = 3 THEN "Order Placed"
//                 END as delivery_status_value')

//             )
//             ->whereNotNull('UCR.order_id')
//             ->orderByDesc('UCR.id')                   
//             ->get();
//         }
            
//             if($rewards->isEmpty()) {
//                 return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage),'get_reward' => false, 'data' => []], 200);
//             }
//             return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'get_reward' => false, 'data' => $rewards], 200);

//  }

 function getOrderByMason(Request $request)
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
                    'user_id' => 'required'
                );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if ($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($validRes['msg'], $targetLanguage)]);
        } 
        $role = $request->user()->role; 
        $id   = $request->user_id;
        // $orderFeedBackWindow = $this->settingVal("setting_name", "order_feedback_window");
        $orderAcknowledgementWindow = $this->settingVal("setting_name", "order_acknowledgement_window");
        
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
            $limit = 6;
            $fetchDataFrom = $limit * ($page - 1);
            $rewards = DB::table('user_catalogue_redeemtions as UCR')
            ->LeftJoin('users as U','U.id','=','UCR.user_id');
               $rewards = $rewards->where('UCR.user_id',$request->user_id); 
            $rewards =  $rewards->select('UCR.id','UCR.order_id','UCR.redeemed_point as point', 'UCR.catalogue_point as catalogue_point',  'UCR.catalogue_tds_point as tds_point', 'UCR.remarks as remarks',
            'UCR.bag','UCR.is_verified','U.name as mason_name',
            'U.aadhaar_no as mason_aadhaar_no',
            'U.phone as mason_phone','UCR.created_at as reward_date',
            'UCR.description','UCR.show_point',
            'UCR.status as delivery_status',
            'UCR.is_delivery_confirmed',
            'UCR.delivery_confirmation_datetime',
            'UCR.comment','UCR.delivery_date', DB::raw("
                CASE
                    WHEN UCR.status IN ('" . UserCatalogueRedeemtion::STATUS_PENDING . "', '" . UserCatalogueRedeemtion::STATUS_REJECTED . "')
                    THEN NULL
                    ELSE UCR.order_tracking_url
                END AS order_tracking_url
            "))
            ->whereNotNull('UCR.order_id')
            ->orderByDesc('UCR.id') 
            ->skip($fetchDataFrom)->take($limit)                  
            ->get();
            foreach($rewards as $key => $rewardVal)
            {
                $is_feedback_button_active = false;
                // $rewards[$key]->mason_name = $this->googleTranslate->translateText($rewardVal->mason_name, $targetLanguage);
                // if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_DELIVERY_ACKNOWLEDGEMENT && !empty($orderFeedBackWindow) && $orderFeedBackWindow > 0 && !empty($rewardVal->delivery_confirmation_datetime))
                // {
                    
                //     $feedbackWindowTime = Carbon::parse($rewardVal->delivery_confirmation_datetime)->copy()->addDays($orderFeedBackWindow);
                //     if(!Carbon::now()->greaterThan($feedbackWindowTime))
                //     {
                //         $is_feedback_button_active = true;
                //     }
                // }
                if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_DELIVERY_ACKNOWLEDGEMENT && !empty($orderAcknowledgementWindow) && $orderAcknowledgementWindow > 0 && !empty($rewardVal->delivery_date))
                {
                    
                    $orderAcknowledgementWindowTime = Carbon::parse($rewardVal->delivery_date)->copy()->addDays($orderAcknowledgementWindow);
                    if(!Carbon::now()->greaterThan($orderAcknowledgementWindowTime))
                    {
                        $is_feedback_button_active = true;
                    }
                }
                
                $rewards[$key]->is_feedback_button_active = $is_feedback_button_active;
                $rewards[$key]->mason_name = $rewardVal->mason_name;
                $rewards[$key]->isConfirmEnabled = false;
                $rewards[$key]->description = $this->googleTranslate->translateText($rewardVal->description, $targetLanguage);
                $deliveryStatusValue = "";
                if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_DELIVERED)
                {
                    $deliveryStatusValue = "Delivered";
                  //  if($rewards[$key]->is_delivery_confirmed != UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_YES)
                    if($rewards[$key]->is_delivery_confirmed == null)
                    {
                        $rewards[$key]->isConfirmEnabled = true;
                    }
                }
                else if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_PENDING)
                {
                    $deliveryStatusValue = "Pending";
                }
                else if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_REJECTED)
                {
                    $deliveryStatusValue = "Rejected";
                }
                else if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_ORDER_PLACED)
                {
                    $deliveryStatusValue = "Order Placed";
                }
                else if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_UNDELIVERED)
                {
                    $deliveryStatusValue = "Undelivered";
                }
                else if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_DELIVERY_ACKNOWLEDGEMENT)
                {
                    $deliveryStatusValue = "Acknowledment of delivery";
                }
                else if($rewardVal->delivery_status == UserCatalogueRedeemtion::STATUS_COMPLAINT_FEEDBACK)
                {
                    $deliveryStatusValue = "Complaint/Feedback";
                }
                $rewards[$key]->delivery_status_value = $this->localLanguageTranslate->translate($deliveryStatusValue, $targetLanguage);
                $rewards[$key]->delivery_confirmed_at = Carbon::parse($rewardVal->delivery_confirmation_datetime)?->toDateTimeString();
            }
        }
        else
        {
            if(empty($orderFeedBackWindow) || $orderFeedBackWindow < 1)
            {
                $orderFeedBackWindow = 0;
            }
            $rewards = DB::table('user_catalogue_redeemtions as UCR')
            ->LeftJoin('users as U','U.id','=','UCR.user_id');
               $rewards = $rewards->where('UCR.user_id',$request->user_id); 
            $rewards =  $rewards->select('UCR.id','UCR.order_id','UCR.redeemed_point as point', 'UCR.catalogue_point as catalogue_point',  'UCR.catalogue_tds_point as tds_point', 'UCR.remarks as remarks',
            'UCR.bag','UCR.is_verified','U.name as mason_name',
            'U.aadhaar_no as mason_aadhaar_no',
            'U.phone as mason_phone','UCR.created_at as reward_date',
            'UCR.description','UCR.show_point',
            'UCR.status as delivery_status',
            'UCR.comment','UCR.delivery_date', 'UCR.delivery_confirmation_datetime',
            DB::raw("
                CASE
                    WHEN UCR.delivery_confirmation_datetime IS NULL THEN FALSE
                    WHEN NOW() > DATE_ADD(UCR.delivery_confirmation_datetime, INTERVAL 3 DAY) THEN FALSE
                    ELSE TRUE
                END AS is_feedback_button_active
            "),
            DB::raw('CASE 
                    WHEN UCR.status = '. UserCatalogueRedeemtion::STATUS_PENDING .' THEN "Pending"
                    WHEN UCR.status = '. UserCatalogueRedeemtion::STATUS_DELIVERED .' THEN "Delivered"
                    WHEN UCR.status = '. UserCatalogueRedeemtion::STATUS_REJECTED .' THEN "Rejected"
                    WHEN UCR.status = '. UserCatalogueRedeemtion::STATUS_ORDER_PLACED .' THEN "Order Placed"
                    WHEN UCR.status = '. UserCatalogueRedeemtion::STATUS_UNDELIVERED .' THEN "Undelivered"
                    WHEN UCR.status = '. UserCatalogueRedeemtion::STATUS_DELIVERY_ACKNOWLEDGEMENT .' THEN "Acknowledment of delivery"
                    WHEN UCR.status = '. UserCatalogueRedeemtion::STATUS_COMPLAINT_FEEDBACK .' THEN "Complaint/Feedback"
                END as delivery_status_value'),
                DB::raw("
                CASE
                    WHEN UCR.status IN ('" . UserCatalogueRedeemtion::STATUS_PENDING . "', '" . UserCatalogueRedeemtion::STATUS_REJECTED . "')
                    THEN NULL
                    ELSE UCR.order_tracking_url
                END AS order_tracking_url
            ")

            )
            ->whereNotNull('UCR.order_id')
            ->orderByDesc('UCR.id')                   
            ->get();
        }
            
            if($rewards->isEmpty()) {
                return response()->json(['status'=> false,'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage),'get_reward' => false, 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'get_reward' => false, 'data' => $rewards], 200);

 }

// public function confirmGiftDelivery_PREV($order_id)
// {
//     try{
//         $userCatalogueRedeemtion = UserCatalogueRedeemtion::find($order_id);
//         if(empty($userCatalogueRedeemtion))
//         {
//             $userCatalogueRedeemtion = UserCatalogueRedeemtion::where("order_id", $order_id)->first();
//             if(empty($userCatalogueRedeemtion))
//             {
//                 return response()->json(['status'=> false,'msg' => "Invalid Order ID.", 'data' => []], 200);
//             }
//         }
//         if($userCatalogueRedeemtion->status != UserCatalogueRedeemtion::STATUS_DELIVERED)
//         {
//             return response()->json(['status'=> false,'msg' => "Order can be confirmed only after Delivery.", 'data' => []], 200);
//         }
//         if($userCatalogueRedeemtion->is_delivery_confirmed == UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_YES)
//         {
//             return response()->json(['status'=> false,'msg' => "Order Delivery Already Confirmed.", 'data' => []], 200);
//         }
//         $userCatalogueRedeemtion->update([
//             "is_delivery_confirmed" => UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_YES,
//             "delivery_confirmation_datetime" => Carbon::now(),
//             "delivery_confirmed_by" => \Auth::user()->id,
//         ]);
//         return response()->json(['status'=> true,'msg' => "Successfully Confirmed.", 'data' => []], 200);
//     }
//     catch(\Exception $e)
//     {
//         return response()->json(['status'=> false,'msg' => "Service is unavailable.", 'data' => [
//             "error_details" => $e->getMessage()
//         ]], 200);
//     }
// } 

public function confirmGiftDelivery(Request $request, $order_id)
{
        $rules = [
            'acknowledgement_status' => 'required|in:0,1',
        ];

      
        $validator  = Validator::make($request->all(), $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            
            return response()->json(['status' => false,'msg' => $res['msg']]);
        }

        try{
            $userCatalogueRedeemtion = UserCatalogueRedeemtion::find($order_id);
            if(empty($userCatalogueRedeemtion))
            {
                $userCatalogueRedeemtion = UserCatalogueRedeemtion::where("order_id", $order_id)->first();
                if(empty($userCatalogueRedeemtion))
                {
                    return response()->json(['status'=> false,'msg' => "Invalid Order ID.", 'data' => []], 200);
                }
            }
            if($userCatalogueRedeemtion->status != UserCatalogueRedeemtion::STATUS_DELIVERED)
            {
                return response()->json(['status'=> false,'msg' => "Order can be confirmed only after Delivery.", 'data' => []], 200);
            }
            if($userCatalogueRedeemtion->is_delivery_confirmed == UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_YES)
            {
                return response()->json(['status'=> false,'msg' => "Order Delivery Already Confirmed.", 'data' => []], 200);
            }
            if(empty($userCatalogueRedeemtion->delivery_date))
            {
                return response()->json(['status'=> false,'msg' => "Order Delivery date is not set.", 'data' => []], 200);
            }
            // $orderAcknowledgementWindow = $this->settingVal("setting_name", "order_acknowledgement_window");
            // if(!empty($orderAcknowledgementWindow) && $orderAcknowledgementWindow > 0 && !empty($userCatalogueRedeemtion->delivery_date))
            // {
            //     $acknowledgementWindowTime = Carbon::parse($userCatalogueRedeemtion->delivery_date)->copy()->addDays($orderAcknowledgementWindow);
            //     if(Carbon::now()->greaterThan($acknowledgementWindowTime))
            //     {
            //         return response()->json(['status'=> false,'msg' => "Order can be confirmed only within ".$orderAcknowledgementWindow." days of post Delivery.", 'data' => []], 200);
            //     }
            // }
            if($request->acknowledgement_status == UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_NO){ 
                  $feedbackEmail = $this->settingVal("setting_name", "feedback_email");
                  
                    //  [As Per Client Requirement Mail Part Is Commented Out Now 07/01/26].

                    // $data['subject'] = 'User Order Not Received.' ;
                    // $data['user'] = \Auth::user()?->name;
                    // $data['email'] = $feedbackEmail;
                    // $data['delivery_status'] = $userCatalogueRedeemtion->status;
                    // $data['name'] = \Auth::user()?->name;
                    // $data['phone'] = \Auth::user()?->phone;
                    // $data['order_id'] = $userCatalogueRedeemtion?->order_id; 
                    // $data['order_date'] = $userCatalogueRedeemtion->created_at->format('d-m-Y H:i A'); 
                    // if(!empty($feedbackEmail)){
                    //     Mail::send('emails.complain', $data, function ($message) use ($data) {
                    //     $message->to($data['email'])
                    //             ->subject($data['subject']);

                    //     });
                    // }
            }
            $userCatalogueRedeemtion->update([
                "is_delivery_confirmed" => $request->acknowledgement_status,
                "delivery_confirmation_datetime" => $request->acknowledgement_status == UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_YES ? Carbon::now() : null,
                "delivery_confirmed_by" => $request->acknowledgement_status == UserCatalogueRedeemtion::IS_DELIVERY_CONFIRMED_YES ? \Auth::user()->id : null,
                "status" => UserCatalogueRedeemtion::STATUS_DELIVERY_ACKNOWLEDGEMENT,
            ]);
            return response()->json(['status'=> true,'msg' => "Successfully Confirmed.", 'data' => []], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['status'=> false,'msg' => "Service is unavailable.", 'data' => [
                "error_details" => $e->getMessage()
            ]], 200);
        }
} 

public function lastOrderContactDetais(Request $request) {

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
                'user_id' => 'required'
            );
    $validator  = Validator::make($input, $rules);
    $validRes = validateInput($validator);
    if ($validRes['status'] == false) {
        return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($validRes['msg'], $targetLanguage)]);
    }

    $redeemtion = UserCatalogueRedeemtion::where(['user_id'=> $request->user_id])->latest()->take(1)->first();

    $email = $redeemtion?->email ?? \Auth::user()?->email;
    $contact = $redeemtion?->phone ?? \Auth::user()?->phone ;


        return response()->json(['status'=> true,'msg' => $this->localLanguageTranslate->translate("Data_got_successfully", $targetLanguage), 'data' => ['email'=> $email, 'contact'=> $contact]], 200);
    
}



}

