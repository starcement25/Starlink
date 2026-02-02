<?php

namespace App\Http\Controllers\API\V1\Church;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\V1\Church;
class ChurchController extends Controller
{
     function index(Request $request)
     {
        $churchs = Church::select('id','name','description')->get();  
        $re_message = 'All Church list';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$churchs]);
     }
}
