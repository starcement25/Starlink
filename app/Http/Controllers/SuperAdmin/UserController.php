<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\user_master;
class UserController extends Controller
{
    function index()
    {
        $users = user_master::where('role','1')->get();
        return view('superadmin/users')->with(['users'=>$users]);
    }
    function userStatus(Request $req){
    	
    	$upd = user_master::where('id',$req->id)->first(); 
		if($req->status ==0)
		{
    	 $upd->active=1;
		}
	    else
		{
		 $upd->active=0;
		}
    	$upd->save();
    	
    	$users = user_master::orderBy('id','DESC')->get();
    	//return view('admin.user-list')->with(['users'=>$users]);
		return back()->with('success','<strong>Success!</strong> User Status Updated Successfully.');
    }
    function addUser()
    {
         return view('superadmin/add-user');
    }
    function addHostUser()
    {
         return view('superadmin/add-host');
    }
    function addAdminUser()
    {
         return view('superadmin/add-admin');
    }
    function updateUser($id)
    {
        $user = user_master::where('id', $id)->first();
        return view('superadmin/update-user')->with(['user'=>$user]);
    }
    function saveUser(Request $req)
    {
        $req->validate([
            'username' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'usertype' => 'required'
        ]);
        if($req->user_id)
        {
            $user = user_master::where('id', $req->user_id)->first();
            $user->email = $req->email;
            $user->mobile = $req->mobile;
            $user->role = $req->usertype;
            $user->user_fn = $req->username;
            $user->save();
            return back()->with('success','<strong>Success!</strong> Updated Successfully');

        }else{
            $qry = user_master::where('email', $req->email)->orWhere('mobile',$req->mobile)->first();    
            if($qry)
            {
                return back()->with('success','<strong>Sorry!</strong> User already exist.');
            }else
            {
                $user = new user_master;
            }
            $user->email = $req->email;
            $user->mobile = $req->mobile;
            $user->role = $req->usertype;
            $user->user_fn = $req->username;
            $user->pass = rand(0,99999);
            $user->save();
            $msg = 'Congratulation! You have registered successfully  Your iPark email:'.$user->email." and Password:".$user->pass;
            mail($user->email,"Password",$msg);
            return back()->with('success','<strong>Success!</strong> User Registered Successfully.The Passowrd sent to the registered email');
        }   
    }
    function deleteUser(Request $req){
    	$qry = user_master::where('id', $req->id)->first(); 
    	$qry->delete();
    	return back()->with('success','<strong>Success!</strong> User Deleted Successfully.');
    }
  
}
