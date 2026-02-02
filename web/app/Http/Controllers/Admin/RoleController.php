<?php

namespace App\Http\Controllers\Admin;

use Flash;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\RoleHasPermission;
use App\Repositories\RoleRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;

class RoleController extends AppBaseController
{
    /** @var RoleRepository $roleRepository*/
    private $roleRepository;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the Role.
     */
    public function index(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('roles.view') ;
        $roles = Role::where('is_reserved_role',0)->paginate(10);
        return view('admin.roles.index')
            ->with('roles', $roles);
    }

    /**
     * Show the form for creating a new Role.
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('roles.create') ;
        $menus = Menu::with('permissions')->get();
        return view('admin.roles.create')->with([
            'role' => "",
            'menus' => $menus,
            'roleHasPermission' => []
        ]);
    }

    /**
     * Store a newly created Role in storage.
     */
    public function store(CreateRoleRequest $request)
    {
        //return $request->all();
        \Helper::checkIsUserAuthorizeToPerformTheTask('roles.create') ;

        if($request->permissions!="")
        {
            $input = $request->all();
            $role = $this->roleRepository->create($input);
            
            foreach($request->permissions as $permission)
            {
                
                $ob= new RoleHasPermission();
                $ob->role_id = $role->id;
                $ob->permission_id = $permission;
                $ob->save();
            }

            
        }
        else{
            Flash::error('Atleast one permission is required.');
            return redirect()->back();
        }

        Flash::success('Role saved successfully.');



        return redirect(route('roles.index'));
    }

    /**
     * Display the specified Role.
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('roles.view') ;
        // $role = $this->roleRepository->find($id);

        // if (empty($role)) {
        //     Flash::error('Role not found');

        //     return redirect(route('roles.index'));
        // }

        // return view('admin.roles.show')->with('role', $role);
        return redirect(route('roles.index'));
    }

    /**
     * Show the form for editing the specified Role.
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('roles.edit') ;
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }
        
        if($role->is_reserved_role == 1)
        {
            Flash::error('Reserved Roles can\'t be view.');

            return redirect(route('roles.index'));
        }

        $menus = Menu::with('permissions')->get();

        $roleHasPermission = RoleHasPermission::where('role_id',$id)->pluck('permission_id')->toArray();
        //return $roleHasPermission;
        return view('admin.roles.edit')->with([
            'role' => $role,
            'menus' => $menus,
            'roleHasPermission' => $roleHasPermission
        ]);
    }

    /**
     * Update the specified Role in storage.
     */
    public function update($id, UpdateRoleRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('roles.edit') ;
        //return $request->permissions;
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }

        $role = $this->roleRepository->update($request->all(), $id);

        $roleHasPermission = roleHasPermission::where('role_id',$id)->pluck('permission_id')->toArray();
        //return $request->permissions=="" ? "null":"not";
        if($request->permissions!="")
        {
            //delete 
            foreach($roleHasPermission as $val)
            {
                if(!in_array($val,$request->permissions))
                {
                    $ob = RoleHasPermission::where([
                        'role_id'=> $id,
                        'permission_id' => $val
                    ])->first();
                    $ob->delete();
                }
            }
            //insert
            foreach($request->permissions as $permission)
            {
                if(!in_array($permission,$roleHasPermission))
                {
                    $ob= new RoleHasPermission();
                    $ob->role_id = $id;
                    $ob->permission_id = $permission;
                    $ob->save();
                }
            }

            
        }
        else{
            Flash::error('Atleast one permission is required.');
            return redirect()->back();
        }
        Flash::success('Updated successfully.');
        return redirect(route('roles.index'));
    }

    /**
     * Remove the specified Role from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('roles.delete') ;
        // $role = $this->roleRepository->find($id);

        // if (empty($role)) {
        //     Flash::error('Role not found');

        //     return redirect(route('roles.index'));
        // }

        // $this->roleRepository->delete($id);

        // Flash::success('Role deleted successfully.');

        return redirect(route('roles.index'));
    }
}
