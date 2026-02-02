<?php
namespace App\Traits;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Reward;
use App\Models\Lifting;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Log;
use App\Models\Setting;
use App\Models\MasonCategory;
use App\Models\CustomerLifting;
use App\Models\RejectedRedeemtion;
use Illuminate\Support\Facades\DB;
use App\Models\UserCatalogueRedeemtion;
use Illuminate\Support\Facades\File;

trait HelperTrait{

    public function isRoleAbleToRejectRedemption($roleID)
    {
        if(!\Auth::check())
        {
            return false;
        }
        if(in_array(\Auth::user()?->role, [User::ROLE_ADMIN, User::ROLE_ACCOUNTS]))
        {
            return true;
        }
        return false;
    }
    
    public function getMasonCategoryByPoint($point)
    {
        return MasonCategory::where("from_point", "<=", $point)->where("to_point", ">=", $point)->first();
    }

    public function getReservedRoleId($roleName)
    {
        return Role::where([
            'role_name' => $roleName,
            'is_reserved_role' => 1,
        ])->first()->id ?? null;
    }
    
    // For File Upload
    public function uploadFile($file, $folder = "")
    {
        $filename = $this->getUniqueId() . "." . $file->getClientOriginalExtension();
        $location = public_path(). '/'. $folder;
        $file->move($location, $filename);
        return ['fileName'=> $filename, "path" => $folder."/".$filename] ;
    }
    // For File Upload from link
    public function uploadDownloadedFileFromLink($content, $folder = "")
    {
        $contentType = $content->header('Content-Type');
        $imageExtension = match ($contentType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/jpeg' => 'jpeg',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $filename = $this->getUniqueId() . "." . $imageExtension;
        $location = public_path(). '/'. $folder;
        if (!File::exists($location)) {
            File::makeDirectory($location, 0755, true);
        }

        // Store the file
        $fullPath = $location . '/' . $filename;
        File::put($fullPath, $content->body());
        return ['fileName'=> $filename, "path" => $folder."/".$filename] ;
    }
    public function uploadData($file, $folder = "")
    {
       // $filename = $this->getUniqueId() . "." . $file->getClientOriginalExtension();
        $filename = $file->getClientOriginalName() ;
        $location = public_path(). '/'. $folder;
        $file->move($location, $filename);
        
        return ['fileName'=> $filename, "path" => $folder."/".$filename] ;
    }

    public function uploadAadhaarDoc($file, $folder = "")
    {
       // $filename = $this->getUniqueId() . "." . $file->getClientOriginalExtension();
        $filename = $this->getUniqueId() . "." . $file->getClientOriginalExtension();
        $location = '/var/www/html/public/aadhaar'. $folder;
        $file->move($location, $filename);
        
        return ['fileName'=> $filename, "path" => $folder.$filename] ;
    }

    public function uploadBulkAadhaarDoc($file, $folder = "")
    {
       // $filename = $this->getUniqueId() . "." . $file->getClientOriginalExtension();
        //$filename = $this->getUniqueId() . "." . $file->getClientOriginalExtension();
        $filename = $file->getClientOriginalName() ;
        $location = '/var/www/html/public/aadhaar'. $folder;
        $file->move($location, $filename);
        
        return ['fileName'=> $filename, "path" => $folder.$filename] ;
    }

    //Get Branch ID By Branch Code
    public function getBranchIdByCode($branchCode)
    {
        $branchId=Branch::where('branch_code',$branchCode)->first();
        return $branchId->id ?? NULL;
    }

    // Unique no generation.
    public function getUniqueId()
    {
        return md5(microtime().\Config::get('app.key'));
    }

    public function getRegPoint()
    {
        $data = Setting::where('setting_name','registration_point')->first() ;
         if($data){
            return $data->setting_value;
           
        }
        return 0 ;
    }
    
    public function getPoint($productId, $bag)
    {
        $product = Product::with('reward_point')->find($productId) ;
        $point = null ;
        
        if($product->reward_point){
            $point = ($bag * $product->reward_point->point) / $product->reward_point->bag ;
        }
        
        return $point ;
    }

    public function updatePoint($userId)
    {
        //$registrationPoint  = User::find($userId)->registration_point ?? 0;

        $rewardPoint        = Reward::selectRaw("IFNULL(SUM(point), 0) AS `point`")->where('user_id', $userId)
                                ->where('is_verified', 1)->value('point') ;
        
        $redeemedPoint      =   UserCatalogueRedeemtion::selectRaw("IFNULL(SUM(redeemed_point), 0) AS `redeemed_point`")
                                ->where('user_id', $userId)->value('redeemed_point') ;
        
        $rejectionCreditPoint = RejectedRedeemtion::selectRaw("IFNULL(SUM(point_credited), 0) AS `point_credited`")
                                ->where('user_id', $userId)->value('point_credited') ;

        $points = ($rewardPoint - $redeemedPoint + $rejectionCreditPoint) ;

        $user = User::find($userId);
        if(!empty($user)){
            $user->update(["points" => $points]);
        }
        
    }

    public function makeElement($type, $value = "", $name, $id, $title, $required, $isActive)
    {
       $required = !empty($required) ? 'required' : '' ;
       $elements = [
        'select'=> '<select id="#id#" name="#name#" class="form-select" aria-label="Default select example" '.$required.'>
                        <option ="" selected> Select Option </option> 
                        #option# 
                    </select>',
        'searchable_select' => ' <input class="form-control" list="datalist_#id#"  id="input_datalist_#id#" name="#name#" placeholder="#title#" '.$required.'>
                                    <datalist id="datalist_#id#">
                                        #option#
                                    </datalist>',
        'text'  => '<input type="text" class="form-control" id="#id#" name="#name#"  placeholder="" '.$required.'>',
        'heading' => '<div class="col-md-12"><label for="" class="form-label"> #title# </label></div>',
        'label' => '<label for="" class="form-label">#title#</label>',
        'checkbox' => '<div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="#value#" id="#id#" name="#name#" >
                            <label class="form-check-label" for="flexCheckDefault2">
                                 #value#
                            </label>
                        </div>',
        'rank' => ' <div class="full-stars-example-two">
                            <div class="rating-group">
                                <input disabled checked class="rating__input rating__input--none" name="rating_#name#" id="rating_#name#"
                                value="0" type="radio">
                                <label aria-label="1 star" class="rating__label" for="rating_#name#-1"><i
                                    class="rating__icon rating__icon--star fa fa-star"></i></label>
                                <input class="rating__input" name="rating_#name#" id="rating_#name#-1" value="1" onclick="setRanking(\'rating_#name#-1\')" type="radio" '.$required.'>
                                   
                                <label aria-label="2 stars" class="rating__label" for="rating_#name#-2"><i
                                    class="rating__icon rating__icon--star fa fa-star"></i></label>
                                <input class="rating__input" name="rating_#name#" id="rating_#name#-2" value="2"  onclick="setRanking(\'rating_#name#-2\')" type="radio" '.$required.'>
                                <label aria-label="3 stars" class="rating__label" for="rating_#name#-3"><i
                                    class="rating__icon rating__icon--star fa fa-star"></i></label>
                                <input class="rating__input" name="rating_#name#" id="rating_#name#-3" value="3" onclick="setRanking(\'rating_#name#-3\')" type="radio" '.$required.'>
                                <label aria-label="4 stars" class="rating__label" for="rating_#name#-4"><i
                                    class="rating__icon rating__icon--star fa fa-star"></i></label>
                                <input class="rating__input" name="rating_#name#" id="rating_#name#-4" value="4" onclick="setRanking(\'rating_#name#-4\')" type="radio" '.$required.'>
                                <label aria-label="5 stars" class="rating__label" for="rating_#name#-5"><i
                                    class="rating__icon rating__icon--star fa fa-star"></i></label>
                                <input class="rating__input" name="rating_#name#" id="rating_#name#-5" value="5" onclick="setRanking(\'rating_#name#-5\')" type="radio" '.$required.'>
                            </div>
                        </div>',
            ];
        
        $options = $label = "" ;
        $element = $elements[$type];

        // Select
        if($type == "select"){
            if($isActive){
                $options = $this->getOptions($value) ;
                $element = str_replace("#option#", $options, $element);
                $element = str_replace("#name#", $name, $element);
                $element = str_replace("#id#", $id, $element);
            }else{
                $element = '<input type="hidden" name="'.$name.'" id="'.$id.'">';
            }
           
        }
        if($type == "searchable_select"){
            if($isActive){
                $options = $this->getSearchOptions($value) ;
                $element = str_replace("#option#", $options, $element);
                $element = str_replace("#name#", $name, $element);
                $element = str_replace("#id#", $id, $element);
                $element = str_replace("#title#", $title, $element);
            }else{
                $element = '<input type="hidden" name="'.$name.'" id="'.$id.'">';
            }
            
        }

        // Text
        if($type == "text"){
            if($isActive){
                $element = str_replace("#name#", $name, $element);
                $element = str_replace("#id#", $id, $element);
            }else{
                $element = '<input type="hidden" name="'.$name.'" id="'.$id.'">';
            }
        }

        // Heading
        if($type == "heading"){
            $element = str_replace("#title#", $title, $element);
       
        }

        // Checkbox
        if($type == "checkbox"){
            if($isActive){
                $element = str_replace("#value#", $value, $element);
                $element = str_replace("#name#", $name, $element);
                $element = str_replace("#id#", $id, $element);
            }else{
                $element = '' ;
            }
            
       
        }

        // rank
        if($type == "rank"){
            if($isActive){
                $element = str_replace("#name#", $name, $element);
                $element = str_replace("#id#", $id, $element);
            }else{
                $element = '<input type="hidden" name="'.$name.'" id="'.$id.'">';
            }
        }

        // Label
        // if($type == "label"){
        //     $element = str_replace("#title#", $title, $element);
        // }
       if(!empty($title) && $type != "checkbox" && $type != "heading"){
            if($isActive){
                $label = $elements['label'] ;
                $label = str_replace("#title#", $title, $label);
                $element = $label.$element ;
            }
       }
      

        return $element ;
        
    }

    public function getOptions($values)
    {
        $options = "" ;
        $values = json_decode($values, true) ;
        foreach ($values as $key => $value) {
            $options .= '<option value="'.$value.'">'.$value.'</option>';
        }

        return $options;
    }

    public function getSearchOptions($values)
    {
        $options = "" ;
        $values = json_decode($values, true) ;
        foreach ($values as $key => $value) {
            $options .= '<option value="'.$value.'">';
        }

        return $options;
    }
    public function getLiftingAvg($product_id='', $dealer_id='')
    {
        // find the 3 month before month and years
         $curr = date("m-Y");
         $month1 = date("m-Y",strtotime("-2 Months"));
         $month2 = date("m-Y",strtotime("-3 Months"));
    
         $arr1 = explode("-",$month1);
         $arr2 = explode("-",$month2);
    
         $years = array($arr1[1],$arr2[1]);
         $marr1 = ltrim($arr1[0],'0');
         $marr2 = ltrim($arr2[0],'0');    
         $months= array($marr1,$marr2);
  
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

    public function getLifting90($product_id='', $dealer_id='')
    {
     // find the 3 month before month and years
        $curr = date("m-Y");
         $month1 = date("m-Y",strtotime("-2 Months"));
         $month2 = date("m-Y",strtotime("-3 Months"));
    
         $arr1 = explode("-",$month1);
         $arr2 = explode("-",$month2);
    
         $years=array($arr1[1],$arr2[1]);
       $marr1 = ltrim($arr1[0],'0');
       $marr2 = ltrim($arr2[0],'0');    
         $months=array($marr1,$marr2);
  
  
         
        // find the 3 month before month and years
        $liftcount  = Lifting::where('user_id', $dealer_id)->count();
     //   $datas = CustomerLifting::whereIn('year', $years)->whereIn('month', $months)->where('dealer_id', $dealer_id)->where('product_id', $product_id)->sum('quantity');  
     $datas1 = CustomerLifting::where('year', $years[0])->where('month', $months[0])
     ->where('dealer_id', $dealer_id)
     ->where('product_id', $product_id)->sum('quantity');  
    
     $datas2 = CustomerLifting::where('year', $years[1])->where('month', $months[1])
     ->where('dealer_id', $dealer_id)
     ->where('product_id', $product_id)->sum('quantity');  
     
    $datas = $datas1+$datas2;
    //dd($datas);
       if($datas){           
            $avglifts = $datas/2;           
            $res = ($avglifts*90)/100;
           // dd($res);
            return $res;
        }        
        return null ;
    }



 public function getLiftingCurrMonthMason($product_id='', $dealer_id='', $mason_id="")
 {
     // find the current month lifting of masson
       // Get the first day of the current month
        $firstDayOfMonth = Carbon::now()->startOfMonth()->format('d-m-Y');

        // Get the last day of the current month
        $lastDayOfMonth = Carbon::now()->endOfMonth()->format('d-m-Y');
        
           $liftIdArr = DB::table('lifting')
             ->where('user_id', $dealer_id)
            ->whereBetween('lifting_date', [$firstDayOfMonth, $lastDayOfMonth])
           // ->whereRaw("DATE_FORMAT(lifting_date, '%Y-%m-%d') between '{$firstDayOfMonth}' and '{$lastDayOfMonth}'")          
           ->where('product_id', $product_id)
            ->pluck('id')
            ->toArray();
  //dd($liftIdArr);
        $datas = Reward::whereIn('lifting_id', $liftIdArr)
                ->where('user_id', $mason_id)
                ->sum('bag');  
     //dd($datas);
       if($datas){   
            return $datas;
        }        
        return 0 ;
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

    public function settingVal($column, $val)
    {
        return Setting::where($column, $val)->first()->setting_value;
    }

    public function send_fcm_notification($tokens, $title, $body, $data = null) 
    {

        if (!is_array($tokens)) {
            $tokens = array($tokens);
        }

        $access_token = $this->google_auth();

        if (is_array($access_token)) {
            return array("auth_error" => $access_token);
        }

        $project_id = "star-saathi";
        $url = "https://fcm.googleapis.com/v1/projects/" . $project_id . "/messages:send";

        $headers = array(
            "Authorization: Bearer " . $access_token,
            "Content-Type: application/json"
        );

        $results = array();

        foreach ($tokens as $token) {

            $payload = array(
                "message" => array(
                    "token" => $token,
                    "notification" => array(
                        "title" => $title,
                        "body"  => $body
                    ),
                    "data" => $data ? $data : new stdClass()
                )
            );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlerr  = curl_error($ch);

            curl_close($ch);

            $decoded = json_decode($response, true);

            $results[] = array(
                "token" => $token,
                "http_code" => $httpcode,
                "curl_error" => $curlerr ? $curlerr : "none",
                "response" => $decoded ? $decoded : $response
            );
        }

        return $results;
    }

    // public function google_auth() 
    // {

    //     $scope = "https://www.googleapis.com/auth/firebase.messaging";

    //    // $jsonKey = json_decode(file_get_contents($serviceAccountFile), true);
    //     // $jsonKey = \Storage::get('firebase/service-account.json');
    //     $jsonKey = storage_path('app/firebase/service-account.json');
    //     if (!$jsonKey) {
    //         return array("error" => "Invalid service account JSON file");
    //     }

    //     $jsonKey = json_decode(file_get_contents($jsonKey), true);

    //     $header = base64_encode(json_encode(array(
    //         "alg" => "RS256",
    //         "typ" => "JWT"
    //     )));

    //     $now = time();

    //     $jwt_claim = base64_encode(json_encode(array(
    //         "iss" => $jsonKey["client_email"],
    //         "scope" => $scope,
    //         "aud" => "https://oauth2.googleapis.com/token",
    //         "iat" => $now,
    //         "exp" => $now + 3600
    //     )));

    //     $data = $header . '.' . $jwt_claim;

    //     openssl_sign($data, $signature, $jsonKey["private_key"], "sha256WithRSAEncryption");
    //     $jwt = $data . '.' . base64_encode($signature);

    //     $postFields = http_build_query(array(
    //         "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
    //         "assertion"  => $jwt
    //     ));

    //     $ch = curl_init("https://oauth2.googleapis.com/token");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $result = json_decode($response, true);

    //     if (!isset($result["access_token"])) {
    //         return array("error" => "Failed to fetch access token", "response" => $response);
    //     }

    //     return $result["access_token"];
    // }

    public function google_auth()
    {
        $scope = "https://www.googleapis.com/auth/firebase.messaging";

        $filePath = storage_path('app/firebase/service-account.json');
    // $filePath = public_path('firebase/service-account.json');

        if (!file_exists($filePath)) {
            return ["error" => "Service account file not found"];
        }

        $jsonKey = json_decode(file_get_contents($filePath), true);

        if (!$jsonKey || !isset($jsonKey['client_email'], $jsonKey['private_key'])) {
            return ["error" => "Invalid service account JSON file"];
        }

        $header = rtrim(strtr(base64_encode(json_encode([
            "alg" => "RS256",
            "typ" => "JWT"
        ])), '+/', '-_'), '=');

        $now = time();

        $jwt_claim = rtrim(strtr(base64_encode(json_encode([
            "iss"   => $jsonKey["client_email"],
            "scope" => $scope,
            "aud"   => "https://oauth2.googleapis.com/token",
            "iat"   => $now,
            "exp"   => $now + 3600,
        ])), '+/', '-_'), '=');

        $data = $header . '.' . $jwt_claim;

        openssl_sign(
            $data,
            $signature,
            $jsonKey["private_key"],
            "sha256WithRSAEncryption"
        );

        $jwt = $data . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        $postFields = http_build_query([
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "assertion"  => $jwt
        ]);

        $ch = curl_init("https://oauth2.googleapis.com/token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (!isset($result["access_token"])) {
            return [
                "error" => "Failed to fetch access token",
                "response" => $result
            ];
        }

        return $result["access_token"];
    }

    public function getNotificationMessage($orderStatus, $giftTitle, $orderId)
    {
        $status = ['0'=> 'Your order for '.$giftTitle.' (Order ID: '.$orderId.') is pending approval.',
                   '1'=> 'Your order for '.$giftTitle.' (Order ID: '.$orderId.') has been delivered. Please acknowledge the order.',
                   '2'=> 'Your order for '.$giftTitle.' (Order ID: '.$orderId.') has been rejected.',
                   '3'=> 'Your order has been placed for '.$giftTitle.' (Order ID: '.$orderId.') .',
                   '4'=> 'Your order for '.$giftTitle.' (Order ID: '.$orderId.') could not be delivered. Please contact support.',
                 ] ;
        
        return $status[$orderStatus] ?? "" ;
    }

    public function createLog($data){

    $log = Log::create($data);

    return $log ;
        
    }

    
}