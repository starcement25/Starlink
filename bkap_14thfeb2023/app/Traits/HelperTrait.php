<?php
namespace App\Traits;

use App\Models\User;
use App\Models\Reward;
use App\Models\Product;
use App\Models\UserCatalogueRedeemtion;

trait HelperTrait{

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
            $point = ($bag * $product->reward_point->point) / $product->reward_point->bag ;
        }
        
        return $point ;
    }

    public function updatePoint($userId)
    {
     
        $registrationPoint  = User::find($userId)->registration_point ?? 0;

        $rewardPoint        = Reward::selectRaw("IFNULL(SUM(point), 0) AS `point`")->where('user_id', $userId)->where('is_verified', 1)->value('point') ;
        
        $redeemedPoint      = UserCatalogueRedeemtion::selectRaw("IFNULL(SUM(redeemed_point), 0) AS `redeemed_point`")->where('user_id', $userId)->value('redeemed_point') ;

        $points = ($registrationPoint + $rewardPoint - $redeemedPoint) ;
       //  dd($points);
        $user = User::find($userId);
        if(!empty($user)){
            $user->update(["points" => $points]);
        }
    }
}