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
class ProductController extends Controller
{
    function getAllProduct(Request $request)
    {
        $products = Product::orderBy('name','ASC')->get();
        return response()->json(['status'=> true, 'data' => $products, 'msg' => "Product data fetch successfully"], 200);
    }
}
