<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\LiftingEnquiry;
use App\Models\User;
use App\Models\Product;
use App\Models\MasonDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleTranslateService;
use App\Utils\LocalLanguageTranslation;
use Carbon\Carbon;
class LiftingEnquiryController extends Controller
{
    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }
    function getLiftingEnquiries(Request $request)
    {
        try{
            if(\Auth::user()->role == User::MASON)
            {
                if(!empty(\Auth::user()->preferred_app_lang))
                {
                    $targetLanguage = \Auth::user()->preferred_app_lang;
                    $page = 1;
                    if($request->has("page") && $request->page != null)
                    {
                        $page = $request->page;
                    }
                    if($page < 1)
                    {
                        return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 400);
                    }
                    $limit = 6;
                    $fetchDataFrom = $limit * ($page - 1);
                    $liftingEnquiries = LiftingEnquiry::select([
                        'id',
                        'enquiry_by',
                        'enquiry_to',
                        'product_id',
                        'quantity',
                        \DB::raw("DATE_FORMAT(date_of_lifting, '%Y-%m-%d') as date_of_lifting"),
                        'lifting_query'
                    ])->where("enquiry_by", \Auth::user()->id)->skip($fetchDataFrom)->take($limit)->get();
                    if($liftingEnquiries->isEmpty())
                    {
                        return response()->json(['status'=> false, 'data' => [], 'msg' => $this->localLanguageTranslate->translate("No_data_found", $targetLanguage)], 404);
                    }
                    foreach($liftingEnquiries as $key => $liftingEnquiry)
                    {
                        $liftingEnquiries[$key]->lifting_query = $this->googleTranslate->translateText($liftingEnquiry->lifting_query, $targetLanguage);
                    }
                }
                else
                {
                    $liftingEnquiries = LiftingEnquiry::select([
                        'id',
                        'enquiry_by',
                        'enquiry_to',
                        'product_id',
                        'quantity',
                        \DB::raw("DATE_FORMAT(date_of_lifting, '%Y-%m-%d') as date_of_lifting"),
                        'lifting_query'
                    ])->where("enquiry_by", \Auth::user()->id)->get();
                    if($liftingEnquiries->isEmpty())
                    {
                        return response()->json(['status'=> false, 'data' => [], 'msg' => $this->localLanguageTranslate->translate("No_data_found", \Auth::user()->preferred_app_lang)], 404);
                    }
                }
                return response()->json(['status'=> true, 'data' => $liftingEnquiries, 'msg' => $this->localLanguageTranslate->translate("Lifting_enquiries_data_fetched_successfully", \Auth::user()->preferred_app_lang)], 200);
            }
            else
            {
                return response()->json(['status'=> false, 'data' => [], 'msg' => $this->localLanguageTranslate->translate('Only_mason_has_this_access', \Auth::user()->preferred_app_lang)], 400);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['status'=> false, 'data' => [], 'msg' => $e->getMessage()], 400);
        }
    }

    public function doEnquiry(Request $request)
    {
        try
        {   
            if(\Auth::user()->role == User::MASON)
            {
                $validator = Validator::make($request->all(), [
                    "dealer_id" => "required|integer",
                    "product_id" => "required|integer",
                    "quantity" => "required|integer|min:2",
                    "date_of_lifting" => "required|date|date_format:Y-m-d|after_or_equal:today",
                    "lifting_query" => "required|string",
                ],
                [
                    'date_of_lifting.after_or_equal' => 'The date of lifting cannot be a past date.',
                ],
                [
                    "dealer_id" => "Dealer",
                    "product_id" => "Product"
                ]
                );
                if ($validator->fails()) {
                    return response()->json(['status'=> false, 'data' => [], 'msg' => $this->googleTranslate->translateText($validator->errors()->first(), \Auth::user()->preferred_app_lang)], 422);
                }
                $product = Product::find($request->product_id);
                if(empty($product))
                {
                    throw new \Exception($this->localLanguageTranslate->translate("Invalid_product", \Auth::user()->preferred_app_lang));
                }
                $dealer = User::find($request->dealer_id);
                if(empty($dealer) || !in_array($dealer->role, [User::DEALER]))
                {
                    throw new \Exception($this->localLanguageTranslate->translate("Dealer_not_found", \Auth::user()->preferred_app_lang));
                }
                $masonDealer = MasonDealer::where([
                    "dealer_id" => $dealer->id,
                    "mason_id" => \Auth::user()->id,
                ])->first();
                if(empty($masonDealer))
                {
                    throw new \Exception($this->localLanguageTranslate->translate("Dealer_not_linked_yet", \Auth::user()->preferred_app_lang));
                }
                \DB::beginTransaction();
                $liftingEnquiry = LiftingEnquiry::create([
                    'enquiry_by' => \Auth::user()->id,
                    'enquiry_to' => $dealer->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'date_of_lifting' => $request->date_of_lifting,
                    'lifting_query' => $request->lifting_query
                ]);
                \DB::commit();
                return response()->json(['status'=> true, 'data' => $liftingEnquiry, 'msg' => $this->localLanguageTranslate->translate("Lifting_enquiry_submitted_successfully", \Auth::user()->preferred_app_lang)], 200);
            }
            else
            {
                return response()->json(['status'=> false, 'data' => [], 'msg' => $this->localLanguageTranslate->translate('Only_mason_has_this_access', \Auth::user()->preferred_app_lang)], 400);
            }   
        }
        catch(\Exception $e)
        {
            \DB::rollback();
            return response()->json(['status'=> false, 'data' => [], 'msg' => $e->getMessage()], 400);
        }
    }
}
