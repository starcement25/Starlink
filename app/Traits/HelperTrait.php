<?php
namespace App\Traits;

use App\Models\User;
use App\Models\Reward;
use App\Models\Product;
use App\Models\Role;
use App\Models\UserCatalogueRedeemtion;
use App\Models\Lifting;
use App\Models\Branch;
use App\Models\CustomerLifting;
use App\Models\RejectedRedeemtion;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


trait HelperTrait{

  public function createRedemptionOrderID($redemptionID)
  {
    $is_royalt_points_live = Setting::where('setting_name','is_royalt_points_live')->first() ;
    if(!empty($is_royalt_points_live) && $is_royalt_points_live->setting_value == 1)
    {
      return "SL".str_pad($redemptionID,8,0,STR_PAD_LEFT);
    }
    return "ORD".str_pad($redemptionID,5,0,STR_PAD_LEFT);
  }
  
  public function getReservedRoleId($roleName)
  {
      return Role::where([
          'role_name' => $roleName,
          'is_reserved_role' => 1,
      ])->first()->id ?? null;
  }
  public function getASMIdByBranch($branchId)
  {
      return Branch::find($branchId)->asm_user_id ?? null;
  }
  
    public function uploadFile($file, $folder = "")
    {
        $filename = $this->getUniqueId() . "." . $file->getClientOriginalExtension();
        $location = public_path(). '/'. $folder;
        $file->move($location, $filename);
        return ['fileName'=> $filename, "path" => $folder."/".$filename] ;
    }

    public function getUniqueId()
    {
        return md5(microtime().\Config::get('app.key'));
    }
   
   public function getPoint($productId, $bag)
    {
        $product = Product::with('reward_point')->find($productId) ;
        $point = null ;
        if($product->reward_point){
            $total_bonus_points=0;
                // if($bag >  $product->more_than_bags)
                // {
                //     $total_bonus_points=$product->bonus_points;                    
                // }

            $point = ($bag * $product->reward_point->point) / $product->reward_point->bag ;
            $point = $point + $total_bonus_points;
        }
        
        return $point ;
    }

    public function updatePoint($userId)
    {
     
      //  $registrationPoint  = User::find($userId)->registration_point ?? 0;

        $rewardPoint        = Reward::selectRaw("IFNULL(SUM(point), 0) AS `point`")->where('user_id', $userId)->where('is_verified', 1)->value('point') ;
        
        $redeemedPoint      = UserCatalogueRedeemtion::selectRaw("IFNULL(SUM(redeemed_point), 0) AS `redeemed_point`")->where('user_id', $userId)->value('redeemed_point') ;

        $rejectionCreditPoint = RejectedRedeemtion::selectRaw("IFNULL(SUM(point_credited), 0) AS `point_credited`")
                                  ->where('user_id', $userId)->value('point_credited') ;
        $points = ($rewardPoint - $redeemedPoint + $rejectionCreditPoint) ;
       //  dd($points);
        $user = User::find($userId);
        if(!empty($user)){
            $user->update(["points" => $points]);
        }
    }

    public function calculatePoint($userId)
    {
     
      //  $registrationPoint  = User::find($userId)->registration_point ?? 0;

        $rewardPoint        = Reward::selectRaw("IFNULL(SUM(point), 0) AS `point`")->where('user_id', $userId)->where('is_verified', 1)->value('point') ;
        
        $redeemedPoint      = UserCatalogueRedeemtion::selectRaw("IFNULL(SUM(redeemed_point), 0) AS `redeemed_point`")->where('user_id', $userId)->value('redeemed_point') ;

        $rejectionCreditPoint = RejectedRedeemtion::selectRaw("IFNULL(SUM(point_credited), 0) AS `point_credited`")
                                  ->where('user_id', $userId)->value('point_credited') ;
        $points = ($rewardPoint - $redeemedPoint + $rejectionCreditPoint) ;
      
        return $points;
    }


   

    public function getLiftingAvg($product_id='', $dealer_id='')
    {
        // find the 3 month before month and years
         $curr = date("m-Y");
         $month1 = date("m-Y",strtotime("-2 Months"));
         $month2 = date("m-Y",strtotime("-3 Months"));
     // return  $month2;
         $arr1 = explode("-",$month1);
         $arr2 = explode("-",$month2);
    
         $years=array($arr1[1],$arr2[1]);
       $marr1 = ltrim($arr1[0],'0');
       $marr2 = ltrim($arr2[0],'0');    
         $months=array($marr1,$marr2);
  
        // dd($months);
         // echo $arr1[1];
        // find the 3 month before month and years
    
       // $datas = CustomerLifting::whereIn('year', $years)->whereIn('month', $months)->where('dealer_id', $dealer_id)->where('product_id', $product_id)->sum('quantity');  
        
     $datas1 = CustomerLifting::where('year', $years[0])->where('month', $months[0])
     ->where('dealer_id', $dealer_id)
     ->where('product_id', $product_id)->sum('quantity');  
    
     $datas2 = CustomerLifting::where('year', $years[1])->where('month', $months[1])
     ->where('dealer_id', $dealer_id)
     ->where('product_id', $product_id)->sum('quantity');  
     
    $datas = $datas1+$datas2;
      
    
    // dd($datas);
       // $liftcount  = Lifting::where('user_id', $dealer_id)->count();
        //dd( $datas);
        if($datas){           
            $avglifts = $datas/2;
            return $avglifts;
        }        
        return null ;
    }

    public function availStock($product_id='', $dealer_id='', $lifting_date = '')
    {
      $calculatingMonth = 3;
      $monthSkipping = 1;
      $previousMonth = $calculatingMonth + $monthSkipping;
      // find the 3 month before month and years 
      $months = [];
      $years = [];
      $lifting_date_arr = explode('-', $lifting_date);
      for($i = $monthSkipping + 1; $i <= $previousMonth; $i++)
      {
        $count = 0;
        $month = $lifting_date_arr[1] - $i;
        while($month < 1)
        {
          $month += 12; 
          $count++;
        }
        $months[] = $month;
        $years[] = $lifting_date_arr[2] - $count;
      }
        $liftcount  = Lifting::where('user_id', $dealer_id)->count();
      //   $datas = CustomerLifting::whereIn('year', $years)->whereIn('month', $months)->where('dealer_id', $dealer_id)->where('product_id', $product_id)->sum('quantity');  
      $i = 0;
      $datas = 0;
      foreach($months as $val)
      {
        $datas += CustomerLifting::where('year', $years[$i])->where('month', $months[$i])
        ->where('dealer_id', $dealer_id)
        ->where('product_id', $product_id)->sum('quantity');
        $i++;
      } 
      if($datas > 0)
      {           
        $avgStock = $datas / $i;           
        return $avgStock;
      }        
      return $datas;
    }



 public function getLiftingCurrMonthMason($product_id='', $dealer_id='', $mason_id="")
    {
     // find the current month lifting of masson
       // Get the first day of the current month
        $firstDayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

        // Get the last day of the current month
        $lastDayOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        
           $liftIdArr = DB::table('lifting')
             ->where('user_id', $dealer_id)
            // ->whereBetween('lifting_date', [$firstDayOfMonth, $lastDayOfMonth])
           // ->whereRaw("DATE_FORMAT(lifting_date, '%Y-%m-%d') between '{$firstDayOfMonth}' and '{$lastDayOfMonth}'")          
           ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') between '{$firstDayOfMonth}' and '{$lastDayOfMonth}'")          
           ->where('product_id', $product_id)
            ->pluck('id')
            ->toArray();
   // dd($liftIdArr);
        $datas = Reward::whereIn('lifting_id', $liftIdArr)
                ->where('user_id', $mason_id)
                ->where('is_verified', 1)
                ->where('is_bonus', 0)
                ->sum('bag');  
   //  dd($datas);
       if($datas){   
            return $datas;
        }        
        return 0 ;
    }

    public function getCurrentMonthLifting($product_id='', $dealer_id='', $lifting_date = '')
    {
     // find the current month lifting of masson
       // Get the first day of the current month
        //  $firstDayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
         $firstDayOfMonth = date("Y-m-01", strtotime($lifting_date));
       // $firstDayOfMonth = '2023-07-01';

        // Get the last day of the current month
        //  $lastDayOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
         $lastDayOfMonth = date("Y-m-t", strtotime($lifting_date));
        //  dd($firstDayOfMonth." ".$lastDayOfMonth);
        // $lastDayOfMonth = '2023-07-31';
        
           $liftIdArr = DB::table('lifting')
             ->where('user_id', $dealer_id)
            // ->whereBetween('lifting_date', [$firstDayOfMonth, $lastDayOfMonth])
           // ->whereRaw("DATE_FORMAT(lifting_date, '%Y-%m-%d') between '{$firstDayOfMonth}' and '{$lastDayOfMonth}'")          
           ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') >= '{$firstDayOfMonth}' and STR_TO_DATE(lifting_date, '%d-%m-%Y') <= '{$lastDayOfMonth}'")          
           ->where('product_id', $product_id)
            ->pluck('id')
            ->toArray();
    // dd($liftIdArr);

        $datas = Reward::whereIn('lifting_id', $liftIdArr)
                ->where('is_verified', 1)
                ->where('verified_by', null)
                ->where('is_bonus', 0)
                ->sum('bag');  
   //  dd($datas);
       if($datas){   
            return $datas;
        }        
        return 0 ;
    }


    public function getRegPoint()
    {
        $data = Setting::where('setting_name','registration_point')->first() ;
         if($data){
            return $data->setting_value;
           
        }
        return 0 ;
    }


   // Push Notifications
     public static function sendPushNotification($to_app = null, $deviceTokens = [], $message = null, $title = '', $params = [])
     {
 
       if (empty($title))
       {
         // $title = 'New Notification';
         $title = '';
       }
 
       if (!empty($params))
       {
         $params = array_merge($params, ['to_app' => $to_app]);
       }
 
       if(is_array($deviceTokens) && sizeof($deviceTokens) > 0)
       {
         $deviceTokenAndroidArr       = !empty($deviceTokens['device_token_android']) ? $deviceTokens['device_token_android'] : [];
 
         $deviceTokenIosArr           = !empty($deviceTokens['device_token_ios']) ? $deviceTokens['device_token_ios'] : [];
 
 
         //////////////////
         // Android
         //////////////////
         if (!empty($deviceTokenAndroidArr))
         {
           $deviceTokenAndroidArr = array_unique($deviceTokenAndroidArr);
 
           // API access key from Google API's Console
           $api_access_key = 'AAAAJmKebBs:APA91bGlw9WumYUyn0jzUpL4RVzKmzfkLTHSYO4WFFL4Pd6evMZqbclCXdCHEXQmR_MuSw8WcKSz1Ubs59x1Wj6_9tTBZkWWuUHNahSjQZtmeoQYjMvmHe1WYuCTGSD7VWBV0A0Y2yJq';
 
           foreach ($deviceTokenAndroidArr as $deviceTokenAndroid)
           {
             if (!empty($deviceTokenAndroid) && $deviceTokenAndroid != 'NO_DEVICE_TOKEN_FOR_IOS_SIMULATOR')
             {
               if (is_array($deviceTokenAndroid) && sizeof($deviceTokenAndroid) > 0)
               {
                 $registrationIds = $deviceTokenAndroid;
               }
               else
               {
                 $registrationIds = array($deviceTokenAndroid);
               }
 
               // prep the bundle
               $msg = array
               (
                 'message' 	=> $message,
                 'title'		=> $title,
                 //'subtitle'	=> 'Subtitle',
                 //'tickerText'	=> 'Ticker text here',
                 'vibrate'	=> 1,
                 'sound'		=> 1,
                 //'largeIcon'	=> 'large_icon',
                 //'smallIcon'	=> 'small_icon',
               );
 
               if (!empty($params))
               {
                 $msg = array_merge($msg, array('params' => $params));
               }
 
               $fields = array
               (
                 'registration_ids' => $registrationIds,
                 'data'			   => $msg
               );
 
               $headers = array
               (
                 'Authorization: key=' . $api_access_key,
                 'Content-Type: application/json'
               );
 
               $ch = curl_init();
               curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
               curl_setopt( $ch,CURLOPT_POST, true );
               curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
               curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
               curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
               curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
               $result = curl_exec($ch );
               curl_close( $ch );
 
                return $result; 
              // print_r($result);
               exit;
               //\Log::info('Push Result:: App Name: ' . $to_app . ' | Device token Android: ' . $deviceTokenAndroid . ' | Title: ' . $title . ' | Message: ' . $message . ' | Result: ' . $result);
               //echo $result;
             }
           }
         }
 
         //////////////////
         // iOS
         //////////////////
         if (!empty($deviceTokenIosArr))
         {
           
           $deviceTokenIosArr = array_unique($deviceTokenIosArr);
           foreach ($deviceTokenIosArr as $deviceTokenIos)
           {
             // echo '|' . $deviceTokenIos;
             if (!empty($deviceTokenIos) && $deviceTokenIos != 'NO_DEVICE_TOKEN_FOR_IOS_SIMULATOR' && $deviceTokenIos != 'IOS_SIMULATOR')
             {
               // *******************/
               // CUSTOMER
              // *******************/
               if ($to_app == 'customer')
               {
                 $bundleId = ''; # <- Your Bundle ID
               }
 
               // *******************/
              // DRIVER
              //*******************/
               else if ($to_app == 'driver')
               {
                 
                 $bundleId = ''; # <- Your Bundle ID
               }
 
               $production = true;
               // ***************************************************************************
               // NOTE: WE WERE USING THIS FOR SENDING PUSH NOTIFICATION BY USING .pem FILE
               // ***************************************************************************
               $keyFile  = '';               # <- Your AuthKey file
               $keyId    = '';               # <- Your Key ID
               $teamId   = '';               # <- Your Team ID (see Developer Portal)
               $url      = $production ? 'https://api.push.apple.com' : 'https://api.development.push.apple.com';  # <- development url, or use http://api.push.apple.com for production environment
               $url      = trim($url . '/3/device/' . trim($deviceTokenIos));
 
               $msg = array(
                 'title' => $title,
                 'body'  => $message,
               );
 
               if (!empty($params))
               {
                 $msg = array_merge($msg, array('params' => $params));
               }
 
               $msg = '{"aps":{"alert":'.json_encode($msg).',"sound":"cash_register_cha_ching_soundbible.aiff"}}';
 
               $key = openssl_pkey_get_private('file://'.$keyFile);
 
               $header = ['alg' => 'ES256', 'kid' => $keyId];
               $claims = ['iss' => $teamId, 'iat' => time()];
 
               $headerEncoded = $this->base64($header);
               $claimsEncoded = $this->base64($claims);
 
               $signature = '';
               openssl_sign($headerEncoded . '.' . $claimsEncoded, $signature, $key, 'sha256');
               $jwt = $headerEncoded . '.' . $claimsEncoded . '.' . base64_encode($signature);
 
               // only needed for PHP prior to 5.5.24
               if (!defined('CURL_HTTP_VERSION_2_0')) {
                   define('CURL_HTTP_VERSION_2_0', 3);
               }
 
               $headers = [
                 "apns-topic: {$bundleId}",
                 "authorization: bearer $jwt"
               ];
 
               $http2ch = curl_init();
               curl_setopt( $http2ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0 );
               curl_setopt( $http2ch, CURLOPT_URL, $url );
               curl_setopt( $http2ch, CURLOPT_PORT, 443 );
               curl_setopt( $http2ch, CURLOPT_HTTPHEADER, $headers );
               curl_setopt( $http2ch, CURLOPT_POST, true );
               curl_setopt( $http2ch, CURLOPT_POSTFIELDS, $msg );
               curl_setopt( $http2ch, CURLOPT_RETURNTRANSFER, true );
               curl_setopt( $http2ch, CURLOPT_SSL_VERIFYPEER, false );
               curl_setopt( $http2ch, CURLOPT_TIMEOUT, 30 );
               curl_setopt( $http2ch, CURLOPT_HEADER, true );
               curl_setopt( $http2ch, CURLOPT_FOLLOWLOCATION, true );
               curl_setopt( $http2ch, CURLOPT_ENCODING, "" );
               curl_setopt( $http2ch, CURLOPT_AUTOREFERER, true );
               curl_setopt( $http2ch, CURLOPT_CONNECTTIMEOUT, 120 );
               curl_setopt( $http2ch, CURLOPT_MAXREDIRS, 10 );
               $result = curl_exec($http2ch );
 
               if ($result === FALSE) {
                 echo curl_error($http2ch);exit;
                 $status = curl_error($http2ch);
               } else {
                 $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
               }
               curl_close( $http2ch );
 
               if(file_exists($keyFile)){
                 echo 'file exists';
               }else{
                 echo 'file not exists';
               }
               
 
               echo ' | Result: ' . $status;
               exit;
 
               //\Log::info('Push Result:: App Name: ' . $to_app . ' | Device token iOS: ' . $deviceTokenIos . ' | Title: ' . $title . ' | Message: ' . $message . ' | Result: ' . $status . $url);
             }
           }
         }
       }
 
       return ;
     }

    public function settingVal($column, $val)
    {
        return Setting::where($column, $val)->first()->setting_value;
    }

}
