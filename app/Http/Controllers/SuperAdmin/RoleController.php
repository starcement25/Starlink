<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Role;
use App\Model\Permission;
use App\Model\UserRole;
use App\Model\user_master;
use App\Model\Menu;
class RoleController extends Controller
{
    function viewRole()
    {
        $roles = Role::orderBy('id', 'DESC')->get();
        $menus = Menu::get();
        return view('superadmin/roles')->with(['roles'=>$roles,'menus'=>$menus]);
    }

    function createSaveRole(Request $req)
    {   
        if($req->role_id)
        {
            $role = Role::where('id',$req->role_id)->first();
            $role->role_name = $req->role_name;
             $role->save();
            return response()->json(array('success'=> 1,'data' => $role));

        }else
        {
            $req->validate([
                'role_name' => 'required',
            ]);
            $role = new Role;
            $role->role_name = $req->role_name;
            $role->save();
            $roles = Role::orderBy('id', 'DESC')->get();
            $menus = Menu::get();
            return redirect()->route('superadmin.roles')->with(['success' => "Added Successfully",'roles'=>$roles,'menus'=>$menus]);
        }
            
    }
    function assignPermissionToRole(Request $req)
    {
        foreach($req->link_ids as $id)
        {
            $perm = new Permission;
            $perm->links_id = $id;
            $perm->role_id = $req->role_id;
            $perm->save();
        }
        return response()->json(array('success'=> 1,'data' =>''));
    }

    function assignRole(Request $req)
    {
        $req->validate([
            'user_id' => 'required',
            'role_id' => 'required',
        ]);
        $userRole = new UserRole;
        $userRole->user_id = $req->user_id;
        $userRole->role_id = $req->role_id;
        $userRole->save();
        $user_roles = UserRole::get();
        $roles = Role::get();
        $admins = user_master::where('role',3)->get();
        return redirect()->route('superadmin.assign-role')->with(['success'=>'Assign Successfuly','user_roles'=>$user_roles,'admins' => $admins,'roles'=>$roles]);

    }
    function assignRoleView()
    {
        $user_roles = UserRole::get();
        $roles = Role::get();
        $admins = user_master::where('role',3)->get();
        return view('superadmin/assign-role')->with(['user_roles'=>$user_roles,'admins' => $admins,'roles'=>$roles]);
    }

    function deleteRole(Request $req)
    {
        $role = Role::where('id',$req->id)->first();
        $role->delete();
        return response()->json(array('success'=> 1,'data' =>''));
    }
    function deleteAssignRole(Request $req)
    {
        $role = UserRole::where('user_id',$req->user_id)->first();
        $role->delete();
        return response()->json(array('success'=> 1,'data' =>''));
    }
    function checkAssignRole(Request $req)
    {
        $role = UserRole::where('user_id',$req->user_id)->first();
        if($role)
        {
            return response()->json(array('success'=> 1,'data' =>''));
        }else
        {
            return response()->json(array('success'=> 0,'data' =>''));
        }
    }
    function savePermission(Request $req)
    {
        if($req->has('links'))
        {
            $req->role_id;
            $p = Permission::where('role_id',$req->role_id)->first();
            if($p)
            {
                Permission::where('role_id',$req->role_id)->delete();
                foreach($req->links as $link)
                {
                    $pm = new Permission;
                    $pm->role_id = $req->role_id;
                    $pm->link_id = $link;
                    $pm->save();
                }

            }else
            {
                foreach($req->links as $link)
                {
                    $pm = new Permission;
                    $pm->role_id = $req->role_id;
                    $pm->link_id = $link;
                    $pm->save();
                }
            }
            
        }
        $roles = Role::orderBy('id', 'DESC')->get();
        $menus = Menu::get();
        return redirect()->route('superadmin.roles')->with(['success' =>'Permission Seved Successfully','roles'=>$roles,'menus'=>$menus]);

    }

    

}
