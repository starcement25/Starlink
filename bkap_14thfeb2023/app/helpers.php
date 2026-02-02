<?php

use App\Models\MasonDealer;
use App\Models\User;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

  function validationFailer($validator)
  {
    
     if ($validator->fails()) {
         $errors = $validator->errors();
         $e = $errors->first();
         $res = array('status' => false,'msg'=>$e);
         return $res;
     }else
     {
        $res = array('status' => true,'msg'=>'');
        return $res;
     }
  }
  function validateInput($validator)
  {
    
     if ($validator->fails()) {
         $errors = $validator->errors();
         $e = $errors->first();
         $res = array('status' => false,'msg' => $e);
         return $res;
     }else
     {
        $validated = $validator->validated();
        $res = array('status' => true,'msg'=>'Validated successfully', 'validated_data' => $validated);
        return $res;
     }
  }

  function sendOTP($receiverNumber)
  {
      $token = getenv("TWILIO_TOKEN");
      $twilio_sid = getenv("TWILIO_SID");
      $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
      $phone = "+91".$receiverNumber;
      try{
          $twilio = new Client($twilio_sid, $token);
          $verify = $twilio->verify->v2->services($twilio_verify_sid)
          ->verifications
          ->create($phone, 'sms');
          return true;
      }
      catch(\Exception $e ){
          return false;
      }
  }
  function verifyPhoneNumber($phone, $otp)
  {
      $token = getenv("TWILIO_TOKEN");
      $twilio_sid = getenv("TWILIO_SID");
      $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
      try{
         $twilio = new Client($twilio_sid, $token);
         $verification = $twilio->verify->v2->services($twilio_verify_sid)->verificationChecks->create(['code' => $otp, 'to' => "+91".$phone]);
         if ($verification->valid) {
            $phone_verified_at = Carbon::now()->toDateTimeString();
            $user = User::where('phone', $phone)->first();
            if($user){
               $user->phone_verified_at =  $phone_verified_at;
               $user->save();
            }
            return $phone_verified_at;
         }else{
            return false;
         }
      }
      catch(\Exception $e ){
         return false;
      }
  }
  
  function getRoleNameById($role)
  {
   if($role == 1){
      $name = "Technical Engineer";
   }elseif($role == 2){
      $name = "Mason";
   }elseif($role == 3){
      $name = "Dealer";
   }elseif($role == 4){
      $name = "RSSD";
   }else{
      $name = "Admin";
   }
   return $name;
  }

  function getProfile($id){
      $user = User::LeftJoin('mason_categories','mason_categories.id','=','users.category_id')
              ->where('users.id',$id)
              ->select('users.*','mason_categories.name as mason_category')
              ->first()
              ->toArray();
      if($user['parent'] && $user['role'] == 2)
      {
         $te = User::select('id','name','phone','emp_code')->where('id',$user['parent'])->first()->toArray();
         $user = array_merge($user,['te' => $te]);
      }elseif(($user['parent'] && $user['role'] == 3) || ($user['parent'] && $user['role'] == 4))
      {
         $mason = User::select('id','name','phone','parent as te_id')->where('id',$user['parent'])->first()->toArray();
         $te = User::select('name as te_name', 'phone as te_phone','emp_code')->where('id',$mason['te_id'])->first();
         $mason = array_merge($mason,['te_detail' => $te]);
         $user = array_merge($user,['mason' => $mason]);
      }

      if($user['role'] == 2)
      { 
         $dealers = DB::table('mason_dealers as md')->select('u.id','u.name','u.phone','u.emp_code')->where('mason_id',$id)->join('users as u','u.id','=','md.dealer_id')->get()->toArray();
         $user = array_merge($user,['dealers' => $dealers]);
      }    
      $user = array_merge($user,['role_name' => getRoleNameById($user['role'])]);
      ksort($user);
      return $user;

  }
  

  