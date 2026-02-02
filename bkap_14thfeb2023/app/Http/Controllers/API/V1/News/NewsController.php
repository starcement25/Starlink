<?php

namespace App\Http\Controllers\API\V1\News;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\API\V1\News;
use Illuminate\Support\Facades\Validator;
class NewsController extends Controller
{
    function index(Request $request)
    {
        $data = News::select('id','title','image','content','created_at')->orderBy('id','DESC')->get();
       
        if(!$data->isEmpty())
        {
            $re_message = 'News fetched successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
            return response()->json(['status' => false, 'message' => 'News not available', 'data'=>$data]);
        }
       
    }
   function newsDetail(Request $request,$id)
   {
    
   		$news = News::select('id','title','image','content','created_at')->where('id',$id)->first();
   		if($news)
        {
            $re_message = 'News fetched successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$news]);
        }else
        {
            return response()->json(['status' => false, 'message' => 'News not available']);
        }
   }
  function addNews(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'title' => 'required',
                    'content' => 'required',
                    'image'  =>'required',
                 );
        $re_message = "";

        // validation 
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']]);
        }
        //news creation
        $validatedData = $validator->validated();
        if($request->file('image')) {
            $file = $request->file('image');
            $filename = 'News-'.time().".jpeg";
            $location = base_path().'/public/News';
            $file->move($location,$filename);
            $validatedData['image'] = asset('/public/News').'/'.$filename;
         }
       	News::create($validatedData);
		return response()->json(['status' => true, 'message' => 'News added successfully']);
       
    }
}
