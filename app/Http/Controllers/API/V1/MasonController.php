<?php

namespace App\Http\Controllers\API\V1;

use Exception;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Lifting;
use App\Models\MasonDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\DealerLinkageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\DealerLinkings\DealerLinkingRequest;
use App\Services\GoogleTranslateService;
use App\Models\DealerLinkageRequestHistory;
use App\Utils\LocalLanguageTranslation;
use Illuminate\Support\Facades\File;

class MasonController extends Controller
{
    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }
    
    function getMyMason(Request $request)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
        {
            $targetLanguage = \Auth::user()->preferred_app_lang;
        }
        $te_id=Auth::user()->id;
        $users = User::where([
            ['role', 2],
            ['status', 1],
            ['parent',$te_id],
        ]);
        if($request->has("search") && !empty($request->search))
        {
            $searchValue = $request->search;

            $users->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('phone', 'like', "%{$searchValue}%");
            });
        }
        $users->select('users.id as mason_id','users.name as mason_name','users.aadhaar_no as mason_aadhaar_no','users.phone as mason_phone');
        if(!empty($targetLanguage))  
        {
            $page = 1;
            if($request->has("page") && $request->page != null)
            {
                $page = $request->page;
            }
            if($page < 1)
            {
                return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 404);
            }
            $limit = 1000;
            $fetchDataFrom = $limit * ($page - 1);
            $data = $users->skip($fetchDataFrom)->take($limit)->get();
            foreach($data as $key => $dataVal)
            {
                $data[$key]->keyword = $dataVal->mason_name;
                // $data[$key]->mason_name = $this->googleTranslate->translateText($dataVal->mason_name, $targetLanguage);
                $data[$key]->mason_name = $dataVal->mason_name;
            }
        }   
        else
        {
            $data = $users->get();
        }
        // if($request->user()->role == 1){
        //      $users = User::where('role', 2);
        //      $users->select('users.id as mason_id','users.name as mason_name','users.aadhaar_no as mason_aadhaar_no','users.phone as mason_phone');
        //      $data = $users->get();
        // }else
        // {
        //     $data = MasonDealer::join('users','users.id','=','mason_dealers.mason_id')
        //      ->where('dealer_id',$request->user()->id)
        //      ->select('users.id as mason_id','users.name as mason_name','users.aadhaar_no as mason_aadhaar_no','users.phone as mason_phone')
        //      ->get();
        // }
       
        if($data->isEmpty())
        return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_masons_found', $targetLanguage), 'data' => $data]);  
        else
        return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Masons_fetched_successfully', $targetLanguage), 'data' => $data]); 
    }
    function getDealersByMasonId(Request $request)
    {
        $input = $request->all();
        $rules = ['mason_id' => 'required'];
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' =>$res['msg']]);
        }
        $data = DB::table('mason_dealers')
        ->join('users as U','U.id','=','mason_dealers.dealer_id')
        ->where('mason_dealers.mason_id',$request->mason_id)
        ->select('U.id as dealer_id','U.name as dealer_name')
        ->get();
        if($data->isEmpty())
        return response()->json(['status' => false, 'msg' => 'No dealer here', 'data' => $data]);  
        else
        return response()->json(['status' => true, 'msg' => 'dealer get fetch successfully', 'data' => $data]); 

    }
    function getAllMasonRssd(Request $request)
    {
        $search = $request->query('search');
        $slen = strlen($search);
        $colum = 'phone';
        if( $slen == 12)
            $colum = 'aadhaar_no';
        $data = MasonDealer::where('dealer_id',$request->user()->id)->whereHas('mason',function($query) use ($colum,$search) {
            $query->where($colum,'like','%'.$search);
        })->get()->pluck('mason');
        if($data->isEmpty())
        return response()->json(['status' => false, 'msg' => 'No mason here', 'data' => $data]);  
        else
        return response()->json(['status' => true, 'msg' => 'Mason get fetch successfully', 'data' => $data]); 
    }
      
    function getAllDealers(Request $request)
    {
        $user = Auth::user();
        if($user->role == 1)
        {
            $input = $request->all();
            $rules = ['mason_id' => 'required|integer'];
            $validator  = Validator::make($input,$rules);
            $res = validationFailer($validator);
            if ($res['status'] == false) {
                return response()->json(['status' => false,'msg' =>$res['msg']]);
            }
            $mason_id = $request->mason_id;
        }
        else
        {
            $mason_id = $user->id;
        }
        $mason = User::find($mason_id);
        if(!$mason)
        {
            return response()->json(['status' => false,'msg' => "Invalid Mason." ]);
        }
        if($user->role == 1)
        {
            if($mason->parent != $user->id)
            {
                return response()->json(['status' => false,'msg' => "Wrong Mason." ]);
            }
        }
        $dealers_id= MasonDealer::where('mason_id',$mason_id)->pluck('dealer_id');
        // $users = User::whereIn('role',[3,4]);
        $users = User::whereIn('id',$dealers_id)->where('status',1);   
        if(!empty(\Auth::user()->preferred_app_lang ?? null))
        {
            $page = 1;
            if($request->has("page") && $request->page != null)
            {
                $page = $request->page;
            }
            if($page < 1)
            {
                return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
            }
            $limit = 6;
            $fetchDataFrom = $limit * ($page - 1);
            $targetLanguage = (\Auth::user()->preferred_app_lang ?? null);
            $data = $users->skip($fetchDataFrom)->take($limit)->get();
            foreach($data as $key => $val)
            {
                $data[$key]->keyword = $val->name;
                // $data[$key]->name = $this->googleTranslate->translateText($val->name, $targetLanguage);
                $data[$key]->name = $val->name;
            }
        }  
        else
        { 
            $data = $users->get();
        }
               
        if($data->isEmpty())
        return response()->json(['status' => false, 'msg' => 'No Dealer here', 'data' => $data]);  
        else
        return response()->json(['status' => true, 'msg' => 'Dealers get successfully', 'data' => $data]); 
    }
    function getDealersByMasonBranch(Request $request)
    {
        $user = \Auth::user();
        if($user->role != 2)
            return response()->json(['status' => false, 'msg' => 'Only Mason Have this access', 'data' => []]);
        // $skippedDealerIds = json_decode($user->dealer_ids);
        $skippedDealerIds = MasonDealer::where("mason_id", $user->id)->pluck("dealer_id")->toArray();
        // $skippedDealerIds = array_merge($skippedDealerIds, DealerLinkageRequest::where([
        //     "user_id" => $user->id,
        // ])->pluck('dealer_id')->toArray());
        if(!empty(\Auth::user()->preferred_app_lang ?? null))
        {
            $page = 1;
            if($request->has("page") && $request->page != null)
            {
                $page = $request->page;
            }
            if($page < 1)
            {
                return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
            }
            $limit = 6;
            $fetchDataFrom = $limit * ($page - 1);
            $dealers = User::where([
                'role' => 3,
                'branch_id' => $user->branch_id,
                'status' => 1,
            ])->whereNotIn('id', $skippedDealerIds)->skip($fetchDataFrom)->take($limit)->get();
        }
        else
        {
            $dealers = User::where([
                'role' => 3,
                'branch_id' => $user->branch_id,
                'status' => 1,
            ])->whereNotIn('id', $skippedDealerIds)->get();
        }
        if($dealers->isEmpty())
        {
            return response()->json(['status' => false, 'msg' => 'No Dealers.', 'drop_down_placeholder' => $this->localLanguageTranslate->translate('Select_Dealer', (\Auth::user()->preferred_app_lang ?? null)), 'data' => []]);
        }
        $dealerIds = [];
        $targetLanguage = (\Auth::user()->preferred_app_lang ?? null);
        foreach($dealers as $dealer)
        {
            if(!empty(\Auth::user()->preferred_app_lang ?? null))
            {
                array_push($dealerIds, ["value" => $dealer->id, "keyword" => $dealer->name." - ".$dealer->sap_code, "label" => $dealer->name, $targetLanguage." - ".$dealer->sap_code]);
                // array_push($dealerIds, ["value" => $dealer->id, "keyword" => $dealer->name." - ".$dealer->sap_code, "label" => $this->googleTranslate->translateText($dealer->name, $targetLanguage)." - ".$dealer->sap_code]);
            }
            else
            {
                array_push($dealerIds, ["value" => $dealer->id, "label" => $dealer->name." - ".$dealer->sap_code]);
            }
        }

        return response()->json(['status' => true, 'msg' => 'Dealers get successfully', 'drop_down_placeholder' => $this->localLanguageTranslate->translate('Select_Dealer', (\Auth::user()->preferred_app_lang ?? null)), 'data' => $dealerIds]);
    }
    function dealerLinkingRequest(DealerLinkingRequest $request)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
        {
            $targetLanguage = \Auth::user()->preferred_app_lang;
        }
        $user = \Auth::user();
        if($user->role != 2)
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Only_mason_has_this_access', $targetLanguage), 'data' => []]);
        //for more strong security.
        $linkedDealerIds = MasonDealer::where("mason_id", $user->id)->pluck("dealer_id")->toArray();
        // if(count(array_intersect(json_decode($user->dealer_ids), $request->dealer_ids)) > 0)
        if(count(array_intersect($linkedDealerIds, $request->dealer_ids)) > 0)
        {
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Input_data_has_some_value_those_are_already_linked', $targetLanguage), 'data' => []]);
        }
        foreach($request->dealer_ids as $request->dealer_id)
        {
            $dealer = User::find($request->dealer_id);
            if(empty($dealer) || $dealer->role != 3)
                continue;
            $dealerLinkageRequest = DealerLinkageRequest::where([
                "user_id" => $user->id,
                "dealer_id" => $dealer->id,
            ])->first();
            try {
                DB::beginTransaction();
                    if($dealerLinkageRequest != null)
                    {
                        // if($dealerLinkageRequest->status != 1)
                        // {
                            $dealerLinkageRequest->update([
                                "status" => 0,
                                "action_taken_by" => $user->id,
                                "updated_at" => Carbon::now(),
                            ]);
                            DealerLinkageRequestHistory::create([
                                "dealer_linkage_request_id" => $dealerLinkageRequest->id,
                                "user_id" => $user->id,
                                "dealer_id" => $dealer->id,
                                "status" => 0,
                                "action_taken_by" => $user->id,
                            ]);
                        // }
                        // else
                        // {
                        //     continue;
                        // }
                    }
                    else
                    {
                        $dealerLinkageRequest = DealerLinkageRequest::create([
                            "user_id" => $user->id,
                            "dealer_id" => $dealer->id,
                            "status" => 0,
                            "action_taken_by" => $user->id,
                        ]);
                        DealerLinkageRequestHistory::create([
                            "dealer_linkage_request_id" => $dealerLinkageRequest->id,
                            "user_id" => $user->id,
                            "dealer_id" => $dealer->id,
                                "status" => 0,
                                "action_taken_by" => $user->id,
                        ]);
                    }
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json(['status' => false, 'msg' => $this->googleTranslate->translateText($e->getMessage(), $targetLanguage), 'data' => []]);
            }
        }
        return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Request_sent_successfully', $targetLanguage), 'data' => []]);
    }

    function updateMasonByTE(Request $request, $masonId)
    {
        try
        {
            $targetLanguage = null;
            if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
            {
                $targetLanguage = $request->preferred_app_lang;
            }
            if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
            {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }

            $mason = User::find($masonId);
            if(empty($mason))
            {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Mason_not_found", $targetLanguage)]);
            }
            $currentAadhaarDocPath = public_path('aadhaar/').$mason->aadhaar_doc;

            $rules = array(
                "address1" => "required|string",
                "address2" => "nullable|string",
                "city" => "required|string|max:255",
                "state" => "required|string|max:255",
                "district" => "required|string|max:255",
                "country" => "required|string|max:255",
                "pincode" => "required|digits:6",
                "marital_status" => "required|in:0,1",
            );
            if($request->marital_status == 1)
            {
                $rules["spouse_name"] = "required_if:marital_status,1|string|max:255";
                $rules["spouse_dob"] = "required_if:marital_status,1|date";
            }
            $rules["dob"] = "required|date";
            if ($request->has('aadhaar_doc') || !File::exists($currentAadhaarDocPath)) {
                $rules["aadhaar_doc"] = "required|image|max:2048";
            }
            $messages = array(
                "address1.required" => "Address_1_is_required",
                "address1.string" => "Invalid_value_for_address_1",
                "address2.string" => "Invalid_value_for_address_2",
                "city.required" => "City_is_required",
                "city.string" => "Invalid_value_for_city",
                "city.max" => "Value_of_city_should_be_under_255",
                "state.required" => "State_is_required",
                "state.string" => "Invalid_value_for_state",
                "state.max" => "Value_of_state_should_be_under_255",
                "district.required" => "District_is_required",
                "district.string" => "Invalid_value_for_district",
                "district.max" => "Value_of_district_should_be_under_255",
                "country.required" => "Country_name_is_required",
                "country.string" => "Invalid_value_for_country_name",
                "country.max" => "Country_name_should_be_under_255",
                "pincode.required" => "Pincode_is_required",
                "pincode.digits" => "Pincode_should_be_6_digits",
                "marital_status.required" => "Marital_status_is_required",
                "marital_status.in" => "Invalid_value_for_marital_status",
                "spouse_name.required_if" => "Spouse_name_is_required",
                "spouse_name.string" => "Invalid_value_for_spouse_name",
                "spouse_name.max" => "Spouse_name_should_be_under_255",
                "spouse_dob.required_if" => "Spouse_DOB_is_required",
                "spouse_dob.date" => "Spouse_DOB_should_be_a_date",
                "dob.required" => "DOB_is_required",
                "dob.date" => "DOB_should_be_a_date",
                "aadhaar_doc.required" => "Aadhaar_doc_is_required",
                "aadhaar_doc.image" => "Aadhaar_doc_should_be_an_image",
                "aadhaar_doc.max" => "Maximum_size_of_Aadhaar_doc_is_2_MB",
            );
            $attributes = array(
                "dob" => "DOB",
                "spouse_dob" => "spouse DOB",
            );
            // validation 
            $validator  = Validator::make($request->all(),$rules,$messages,$attributes);
            $res = validationFailer($validator);
            if ($res['status'] == false) {
                return response()->json(['status' => false,'msg' => $this->localLanguageTranslate->translate($res['msg'], $targetLanguage)]);
            }
            $validateData = $validator->validated();
            

            if ($request->has('aadhaar_doc') && File::exists($currentAadhaarDocPath)) {
                File::delete($currentAadhaarDocPath);
            }
            if($request->has('aadhaar_doc'))
            {
                $file = $request->file('aadhaar_doc');
                $filename = "M".$mason->id.".".$request->file('aadhaar_doc')->getClientOriginalExtension();
                $location = base_path().'/public/aadhaar';
                $file->move($location,$filename);
                $validateData['aadhaar_doc'] = $filename;
            }
            DB::beginTransaction();
            $mason->update($validateData);
            DB::commit(); 
            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Updated_successfully', $targetLanguage), 'data' =>[]]);
        }
        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Someting_went_wrong", $targetLanguage)]);
        }
    }

    function getMasonByTE(Request $request, $masonId)
    {
        try
        {
            $targetLanguage = null;
            if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
            {
                $targetLanguage = $request->preferred_app_lang;
            }
            if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
            {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            
            $mason = User::find($masonId);
            if(empty($mason))
            {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Mason_not_found", $targetLanguage)]);
            }
            if($mason->parent != \Auth::user()->id)
            {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Wrong_Mason", $targetLanguage)]);
            }
            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Data_got_successfully', $targetLanguage), 'data' => [
                "id" => $mason->id,
                "name" => $mason->name,
                "email" => $mason->email,
                "phone" => $mason->phone,
                "status_value" => $mason->status,
                "status" => $mason->status == 1 ? "Active" : "Inactive",
                "address1" => $mason->address1,
                "address2" => $mason->address2,
                "city" => $mason->city,
                "district" => $mason->district,
                "state" => $mason->state,
                "state" => $mason->state,
                "country" => $mason->country,
                "pincode" => $mason->pincode,
                "dob" => $mason->dob,
                "aadhaar_no" => $mason->aadhaar_no,
                "aadhaar_doc" => is_file(public_path('aadhaar/' . $mason->aadhaar_doc)) ? url('public/aadhaar/'.$mason->aadhaar_doc) : "",
                "marital_status_value" => $mason->marital_status,
                "marital_status" => $mason->marital_status == 1 ? "Married" : "Unmarried",
                "profile_pic" => $mason->profile_pic,
                "te_name" => $mason->te_linked->name ?? "",
                "spouse_name" => $mason->spouse_name,
                "spouse_dob" => $mason->spouse_dob,
            ]]);
        }
        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Someting_went_wrong", $targetLanguage)]);
        }
    }

    function getMasonsByTE(Request $request)
    {
        try
        {
            $targetLanguage = null;
            if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
            {
                $targetLanguage = $request->preferred_app_lang;
            }
            if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
            {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $page = 1;
            if($request->has("page") && $request->page != null && $page > 0)
            {
                $page = $request->page;
            }
            $limit = 1000;
            $fetchDataFrom = $limit * ($page - 1);
            $masons = User::where("parent", \Auth::user()->id)->skip($fetchDataFrom)->take($limit)->get();
            if(count($masons) === 0)
            {
                return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate("No_masons_found", $targetLanguage), 'data' => []]);
            }
            $masonsData = [];
            foreach($masons as $mason)
            {
                array_push($masonsData, [
                    "id" => $mason->id,
                    "name" => $mason->name,
                    "email" => $mason->email,
                    "phone" => $mason->phone,
                    "status_value" => $mason->status,
                    "status" => $mason->status == 1 ? "Active" : "Inactive",
                    "address1" => $mason->address1,
                    "address2" => $mason->address2,
                    "city" => $mason->city,
                    "district" => $mason->district,
                    "state" => $mason->state,
                    "state" => $mason->state,
                    "country" => $mason->country,
                    "pincode" => $mason->pincode,
                    "dob" => $mason->dob,
                    "aadhaar_no" => $mason->aadhaar_no,
                    "aadhaar_doc" => is_file(public_path('aadhaar/' . $mason->aadhaar_doc)) ? url('public/aadhaar/'.$mason->aadhaar_doc) : "",
                    "marital_status_value" => $mason->marital_status,
                    "marital_status" => $mason->marital_status == 1 ? "Married" : "Unmarried",
                    "profile_pic" => $mason->profile_pic,
                    "te_name" => $mason->te_linked->name ?? "",
                    "spouse_name" => $mason->spouse_name,
                    "spouse_dob" => $mason->spouse_dob,
                    "keyword" => $mason->phone,
                ]);
            }
            if(count($masonsData) == 0)
            {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_masons_found', $targetLanguage), 'data' => []]); 
            }
            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Data_got_successfully', $targetLanguage), 'data' => $masonsData]);
        }
        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate("Someting_went_wrong", $targetLanguage)]);
        }
    }
}

