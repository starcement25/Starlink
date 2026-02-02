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

        $rewardPoint        = Reward::selectRaw("IFNULL(SUM(point), 0) AS `point`")->where('user_id', $userId)
                                ->where('is_verified', 1)->value('point') ;
        
        $redeemedPoint      = UserCatalogueRedeemtion::selectRaw("IFNULL(SUM(redeemed_point), 0) AS `redeemed_point`")
                                ->where('user_id', $userId)->value('redeemed_point') ;

        $points = ($registrationPoint + $rewardPoint - $redeemedPoint) ;

        $user = User::find($userId);
        if(!empty($user)){
            $user->update(["points" => $points]);
        }
    }

    public function makeElement($type, $value = "", $name, $id, $title, $required)
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
            $options = $this->getOptions($value) ;
            $element = str_replace("#option#", $options, $element);
            $element = str_replace("#name#", $name, $element);
            $element = str_replace("#id#", $id, $element);
        }
        if($type == "searchable_select"){
            $options = $this->getSearchOptions($value) ;
            $element = str_replace("#option#", $options, $element);
            $element = str_replace("#name#", $name, $element);
            $element = str_replace("#id#", $id, $element);
            $element = str_replace("#title#", $title, $element);
        }

        // Text
        if($type == "text"){
            $element = str_replace("#name#", $name, $element);
            $element = str_replace("#id#", $id, $element);
        }

        // Heading
        if($type == "heading"){
            $element = str_replace("#title#", $title, $element);
       
        }

        // Checkbox
        if($type == "checkbox"){
            $element = str_replace("#value#", $value, $element);
            $element = str_replace("#name#", $name, $element);
            $element = str_replace("#id#", $id, $element);
            
       
        }

        // rank
        if($type == "rank"){
            $element = str_replace("#name#", $name, $element);
            $element = str_replace("#id#", $id, $element);
        }

        // Label
        // if($type == "label"){
        //     $element = str_replace("#title#", $title, $element);
        // }
       if(!empty($title) && $type != "checkbox" && $type != "heading"){
            $label = $elements['label'] ;
            $label = str_replace("#title#", $title, $label);
            $element = $label.$element ;
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
}