<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\MasonDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StaticPage;
use App\Models\ContactPage;
use App\Models\Faq;
use App\Models\Catalogue;
use App\Models\MasonCategory;
use App\Models\SocialLink;
use App\Traits\HelperTrait;
use App\Models\UserCatalogueRedeemtion;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
	use HelperTrait;
    function myProfile(Request $request)
    {
        $user = getProfile($request->user()->id);
        return response()->json(['status'=> true, 'data' => $user, 'msg' => "User data fetch successfully"], 200);
    }
    function updateProfile(Request $request)
    {
        try {
            $input = $request->all();
            $rules = array(
                        'phone' => 'required|min:10|max:10',
                        'email' => 'required|email',
                        'name' => 'required',
                    );
            $validator  = Validator::make($input, $rules);
            $validRes = validateInput($validator);
            if ($validRes['status'] == false) {
                return response()->json(['status' => false, 'msg' => $validRes['msg']]);
            }
            $user_id = $request->user()->id;
            $data = $validRes['validated_data'];
            if($request->aadhaar_no) {
                $data['aadhaar_no'] = $request->aadhaar_no;
            }
            User::where('id',$user_id)->update($data);
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Updated Successfully']);   
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => "Someting went wrong"]);
        }
    }
    function changeProfilePic(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'pic' => 'mimes:jpeg,jpg,png|required',
                );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if ($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $validRes['msg']]);
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
            return response()->json(['status' => true, 'msg' => 'profile pic updated successfully', 'data' =>['new_pic' => $picUrl]]);
        }
    }


    function getAbout(Request $request)
    {   
        $datas =  StaticPage::where('page_slug','about-us')->first();
            if(empty($datas)) {
                return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }

    function getPrivacy(Request $request)
    {   
        $datas =  StaticPage::where('page_slug','privacy-policy')->first();
            if(empty($datas)) {
                return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }

    function getTerms(Request $request)
    {   
        $datas =  StaticPage::where('page_slug','terms-and-conditions')->first();
            if(empty($datas)) {
                return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }

    function getContact(Request $request)
    {   
        $datas =  ContactPage::first();
            if(empty($datas)) {
                return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }

    function getFAQ(Request $request)
    {   
        $datas =  Faq::get();
            if(empty($datas)) {
                return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }

    function getGiftLink(Request $request)
    {   
        // $datas =  Catalogue::get();
         $datas =  MasonCategory::with('catalogues')->get();
            if(empty($datas)) {
                return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }

    function getSocialLink(Request $request)
    {   
        $datas =  SocialLink::get();
            if(empty($datas)) {
                return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "data get successfully", 'data' => $datas], 200);
    }

    function getNotification(Request $request)
    {   
        $datas =  Notification::get();
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


    function applyRedeemtion(Request $request)
    {   
  
        $input = $request->all();
        $rules = array(
            'role' => 'required',
            'catalogue_id' => 'required',
            'user_id' => 'required',
            'redeemed_point' => 'required'
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $res['msg']]);
        }

        if($request->role == 2)
        {
            // for add redeemtion points
            $userCat =  new UserCatalogueRedeemtion;
            $userCat->user_id = $request->user_id;
            $userCat->catalogue_id = $request->catalogue_id;
            $userCat->redeemed_point = $request->redeemed_point;        
            $userCat->delivery_address = $request->delivery_address;
            $userCat->save();
			
			// update the points of mason
            $this->updatePoint($request->user_id);
                if(empty($userCat)) {
                    return response()->json(['status'=> false,'msg' => "No data found", 'data' => []], 200);
                }
                return response()->json(['status'=> true,'msg' => "Gift Redeemed Successfully", 'data' => $userCat], 200);
        }
        else
        {
            return response()->json(['status'=> false,'msg' => "You're  not able to redeem gift. Only mason can able to redeem the gift.", 'data' => []], 200);
        }
       
           
    }
}

