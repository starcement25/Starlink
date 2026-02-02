<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\MasonDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StaticPage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
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
}

