<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\MasonDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class MasonRegistrationController extends Controller
{
    function register(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'name' => 'required',
                    'phone' => 'required|min:10|max:10,|unique:users,phone',
                    'branch_id'  => 'required',
                    'dealer_ids'   => 'required',
                    'address' => 'required',
                    'dob' => 'required',
                    'aadhaar_no' => "required|min:12|max:12",
                    'marital_status' => "required",
                    'phone_verified_at' => "required",
                 );
                 
        $messages = array(
                       "phone_verified_at.required" => "Please verify phone first",
                       "phone.min" => "invaild phone",
                       "phone.max" => "invailid phone"
                    );
        $attributes = array(
            'name' => 'mason name',
            'branch_id' => 'branch',
        	'dealer_ids' => 'dealer',
        	'aadhaar_no'=> 'aadhaar',
            'marital_status' => 'marital status',
            'phone' => 'mason phone',
            'phone_verified_at' => 'phone verified at'
        );

        // validation 
        $validator  = Validator::make($input,$rules,$messages,$attributes);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' =>$res['msg']]);
        }
        $validatedData = $validator->validated();
        $validatedData['parent'] = $request->user()->id;
        $validatedData['role'] = 2;
        $validatedData['spouse_name'] = $request->spouse_name;
        $validatedData['spouse_dob'] = $request->spouse_dob;
        try{
            DB::beginTransaction();
            $user = User::create($validatedData);
            if(isset($user->id)){
                if($request->file('aadhaar_doc')) {
                    $file = $request->file('aadhaar_doc');
                    $filename = "M".$user->id.".".$request->file('aadhaar_doc')->getClientOriginalExtension();
                    $location = base_path().'/public/aadhaar';
                    $file->move($location,$filename);
                    $user->aadhaar_doc = $filename;
                    $user->save();
                }
                $dealers =  json_decode($request->dealer_ids);
                $m_d = array();
                foreach($dealers as $dealer){
                    $m_d[] = array('mason_id' => $user->id, 'dealer_id' => $dealer);
                } 
                MasonDealer::insert($m_d);
                DB::commit(); 
                return response()->json(['status'=> true, 'data' => $user, 'msg' => "Mason Registration sucessfully"]);
            }
           
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['status'=> false, 'msg' => $e->getMessage()]);
        }
        

    }
    
    function verifyPhone(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'phone' => 'required|min:10',
            'otp' => 'required'
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $res['msg']]);
        }
        $phone_verified_at = verifyPhoneNumber($request->phone, $request->otp);
        if($phone_verified_at){
            return response()->json(['status'=> true, 'msg' => "phone verified successfully",'data' => ['phone_verified_at' => $phone_verified_at]]);
        }else{
            return response()->json(['status' => false,'msg' => 'invailid OTP']);
        }
        

    }
}

