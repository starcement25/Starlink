<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\user_master;
use App\Model\Settings;
class AjaxController extends Controller
{
    function userlist(Request $req)
    {
        $users = user_master::where('role',$req->usertype)->get();
        
        if(!$users->isEmpty())
        {
            
           
            return response()->json(array('success'=> 1,'data' => $users));
        }
        
    }
    function settingsChange(Request $req)
    {
        $set = Settings::where('name',$req->name)->first();
       
        if($set)
        {
            $set->value = $req->value;
            $set->save();
            return response()->json(array('success'=> 1,'data' => ''));
        }else
        {
            $set = new Settings;
            $set->value = $req->value;
            $set->name =  $req->name;
            $set->save();
            return response()->json(array('success'=> 1,'data' => ''));
        }
    }
    public function changeLogo(Request $request)
    {
         
        $validatedData = $request->validate([
         'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if($request->name == 'header_logo')
        {
            $name = 'header-logo.jpg';
        }else
        {
            $name = 'footer-logo.jpg';
        }
       
        $path = public_path('/logo/');

        if($request->hasFile('image'))
        {   
            $file =  $request->file('image');
        }else
        {
            $file =  $request->file('image2');
        }
        $file->move($path, $name);
        $save = Settings::where('name',$request->name)->first();
        $save->value = $name;
        $save->save();
        return response()->json($path);
 
    }
}
