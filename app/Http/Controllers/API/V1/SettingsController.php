<?php

namespace App\Http\Controllers\API\V1;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Setting;
use App\Models\MasonDealer;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    // All Settings.
    function getAllSettings(Request $request)
    {

        $settings = Setting::where('is_visible', '>', 0)->whereNotIn("setting_name", ["flash_banner"])->get();
       
        $result = [] ;
        foreach ($settings as $key => $item) {
            $result[$item->setting_name]  = $item->setting_value ;
        }
         
        if(empty($result)){
            return response()->json(['status' => false, 'msg' => 'No Settings here', 'data' => []]);  
        }
      
        return response()->json(['status' => true, 'msg' => 'Settings get successfully', 'data' => [$result]]); 
      
    }
    function delete_account_btn()
    {
        $settings = Setting::where('setting_name','delete_account_btn')->pluck('setting_value')->toArray();
        $versionArray=[];
        //$versionArray['btn_status']= $settings[0];
        $val= (int) $settings[0];
        return response()->json(['status' => $val]);
        //return response()->json(['status' => true, 'msg' => 'Status get successfully', 'data' => [$versionArray]]);
    }

    function appVersions()
    {
        $settings = Setting::where('setting_name','android_app_version')->orWhere('setting_name','ios_app_version')->pluck('setting_value')->toArray();
        $versionArray=[];
        $versionArray['android']= $settings[0];
        $versionArray['ios']= $settings[1];
        return response()->json(['status' => true, 'msg' => 'App Versions get successfully', 'data' => [$versionArray]]);
    }
    function getFlashBanner()
    {
        $user = Auth::user();
        $data = ["displayBanner" => false];
        if(!($user->role == 1 || $user->role == 2))
        {
            return response()->json(['status' => false, 'msg' => 'Only TE and Mason have this access.', 'data' => [$data]]);
        }
        $settingBannerDisplayCount = Setting::where("setting_name", "flash_banner_display_count")->pluck("setting_value")->first();
        if($settingBannerDisplayCount != null && $settingBannerDisplayCount > 0)
        {
            $settingBanner = Setting::where("setting_name", "flash_banner")->pluck("setting_value")->first();
            if($settingBanner == null || !is_file(base_path()."/web/public/".$settingBanner))
            {
                return response()->json(['status' => false, 'msg' => 'Banner not Set.', 'data' => [$data]]);
            }
            $userVisitBanner = $user->flash_banner_visited;
            $userLastVisitBanner = $user->flash_banner_visited_at;
            $resetUserBannerVisit = true;
            if($userVisitBanner != null && $userLastVisitBanner != null)
            {
                $currentDate = Carbon::now()->toDateString();
                $userLastVisitBanner = Carbon::parse($userLastVisitBanner)->toDateString();
                if($userLastVisitBanner == $currentDate)
                {
                    $resetUserBannerVisit = false;
                    if($userVisitBanner < $settingBannerDisplayCount)
                    {
                        $user->update([
                            "flash_banner_visited" => $userVisitBanner + 1,
                            "flash_banner_visited_at" => Carbon::now()
                        ]);
                    }
                    else
                    {
                        return response()->json(['status' => false, 'msg' => 'User Quota finished.', 'data' => [$data]]);
                    }
                }
            }
            if($resetUserBannerVisit)
            {
                $user->update([
                    "flash_banner_visited" => 1,
                    "flash_banner_visited_at" => Carbon::now()
                ]);
            }
            $data = ["displayBanner" => true, "banner_path" => url("web/public/".$settingBanner)];
            return response()->json(['status' => true, 'msg' => 'Flash Banner Path Get Successfully.', 'data' => [$data]]);
        }
        return response()->json(['status' => false, 'msg' => 'Flash Banner is Inactive.', 'data' => [$data]]);
    }



    public function getMasonList(Request $request, $id)
{
    
    try {
       
        $users = User::where('role', 2)->get();
dd($users);
        $data_arr = [];

       
        foreach ($users as $user) {
            if ($user->te_linked && $user->te_linked->emp_code == $id) {
                $data_arr[] = [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'phone' => $user->phone,
                ];
            }
        }

        return response()->json([
            'status' => true,
            'msg'    => $this->localLanguageTranslate->translate('Data_of_query_status_got_successfully', $targetLanguage ?? 'en'),
            'data'   => $data_arr,
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => false,
            'msg'    => $e->getMessage(),
            'data'   => [],
        ]);
    }
}
}

