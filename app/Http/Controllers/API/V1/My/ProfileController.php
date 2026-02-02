<?php

namespace App\Http\Controllers\API\V1\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
class ProfileController extends Controller
{
    function profile(Request $request)
    {
        $user = $request->user();
        $data = $this->collectdata($user,'User');
        $re_message = 'Profile retrieved successfull';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
    }
    function profileUpdate(Request $request)
    {
       
        $data = $this->collectdata($request,"Request");
        $arow = User::where('id',$request->user()->id)->update($data);
        if($arow)
        {
            $user = User::find($request->user()->id);
            $data = $this->collectdata($user,'User');
            $re_message = 'You have successfully updated profile';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
            $re_message = 'something went wrong';
            return response()->json(['status' => false,'message' => $re_message], 400);
        }


    }
    function updateProfilePic(Request $request)
    {
        $input = $request->all();
        $rules = array(
                'profile_pic' => 'required|mimes:png,jpg,jpeg|max:2048'
                 );
        $attributes = array(
        'profile_pic' => 'Profile Photo',
     
        );
        // validation 
        $validator  = Validator::make($input,$rules,[],$attributes);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $e = $errors->first();
            return response()->json(['status' => false,'message' =>$e], 400);
        }
        if($request->file('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = 'P'.auth()->user()->id.".jpeg";
            $location = base_path().'/public/profile';
            $file->move($location,$filename);
            $profile_pic = $location.'/'.$filename;
         }
        $user = User::find($request->user()->id);
        $user->profile_pic = $profile_pic;
        $user->save();
        $data['profile_pic'] = $user->profile_pic;
        $re_message = 'You have successfully updated profile pic';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
    }

    function collectdata($object,$class)
    {
       if($class == "User")
       {
         $church = $object->church();
       }else
       {
         $church = $object->church_id;
       }
    

        $data = array(
            'id' =>auth()->user()->id,
            'f_name' => $object->f_name,
            'l_name' => $object->l_name,
            'phone' => $object->phone,
            'email' => $object->email,
            'dob' => $object->dob,
            'gender' => $object->gender,
            'church' => $church,
            'marrige_status' => $object->marrige_status,
            'occupation' => $object->occupation,
            'number_of_childeren' =>$object->number_of_childeren,
            'volunteer' => $object->volunteer,
            'country' => $object->country,
            'city' => $object->city,
            'zipcode' => $object->zipcode,
            'address1' => $object->address1,
            'address2' => $object->address2,
            'profile_pic' => auth()->user()->profile_pic,
        );
        return $data;
    }

}
