<?php

namespace App\Http\Controllers\API\V1\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\V1\category;
use App\Models\API\V1\Product;


class CategoryController extends Controller
{
     function index(Request $request)
     {
        $churchs = category::select('id','name','description')->get();  
        $re_message = 'All Category list';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$churchs]);
     }


 function productList($cid,Request $request)
     {
        $datas = Product::where('cate_id',$cid)->get();  
        $re_message = 'All product  list by category';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$datas]);
     }
}
