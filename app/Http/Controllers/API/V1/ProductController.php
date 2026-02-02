<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleTranslateService;
class ProductController extends Controller
{
    protected $googleTranslate;

    public function __construct(GoogleTranslateService $googleTranslate)
    {
        $this->googleTranslate = $googleTranslate;
    }
    function getAllProduct(Request $request)
    {
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
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
            $products = Product::where("status", 1)->skip($fetchDataFrom)->take($limit)->get();
            if($products->isEmpty())
            {
                return response()->json(['status'=> false, 'data' => [], 'msg' => "No Product data found"], 404);
            }
            foreach($products as $key => $product)
            {
                $products[$key]->keyword = $product->name;
                // $products[$key]->name = $this->googleTranslate->translateText($product->name, $targetLanguage);
                $products[$key]->name = $product->name;
                $products[$key]->description = $this->googleTranslate->translateText($product->description, $targetLanguage);
            }
        }
        else
        {
            $products = Product::where("status", 1)->get();
        }
        return response()->json(['status'=> true, 'data' => $products, 'msg' => "Product data fetch successfully"], 200);
    }
}
