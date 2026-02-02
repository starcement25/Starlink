<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $dataTable, Request $request)
    {
        // $users = User::orderBy('id', 'DESC')->get();
        // $roles = ['1'=> 'Technical Engineer', '2'=> 'Dealer', '3'=> 'Mason','4'=> 'RSSD', '5'=> 'Admin'] ;
        
        // foreach ($users as $key => $user) {
        //     $users[$key]->role_name = $roles[$user->role]  ?? "";
          
        // }
        // return view('admin.user.index')->with('users', $users);
        return $dataTable->render('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $status = ['' => 'Select Status', '1'=> 'Married', '2'=> 'Un Married'];
        $rolesArr  = Role::select('id', 'role_name')->pluck('role_name', 'id')->toArray();
       
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches = ['' => 'Select Branch'] + $branchArr ;
        $roles    = ['' => 'Select Role'] + $rolesArr ;
        
        return view('admin.user.create')
                ->with('statusOption', $status)->with('status', "")
                ->with('roleOption', $roles)->with('roleSelected', "")
                ->with('branchOption', $branches)->with('branchSelected', "") ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        if($request->role == '3'){
          $validated =   $request->validate(['aadhaar_no' => 'required']) ;
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        // return $input ;
        $product = User::create($input);
        Flash::success('User saved successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    //    return Branch::with('user')->whereHas('user', function($query){
    //     $query->where('branch_id', 1);
    //    })->get() ;
       //return User::with('branch')->find($id) ;
       return User::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $status = ['' => 'Select Status', '1'=> 'Married', '2'=> 'Un Married'];
        $rolesArr  = Role::select('id', 'role_name')->pluck('role_name', 'id')->toArray();
        $roles    = ['' => 'Select Role'] + $rolesArr ;
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches = ['' => 'Select Branch'] + $branchArr ;
       
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }
        return view('admin.user.edit')
                ->with('user', $user)
                ->with('statusOption', $status)->with('status', $user->status)
                ->with('roleOption', $roles)->with('roleSelected', $user->role)
                ->with('branchOption', $branches)->with('branchSelected', $user->branch_id) 
        ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);
       
        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }
       
        $emailStatus = User::where('email', $request->email)->WhereNotIn('id', [$user->id])->get();

        if(count($emailStatus) > 0){
            return redirect()->back()->withErrors(['email' => 'This email is already taken.'])
                        ->withInput($request->input());
        }
        $input = !empty($request->password) ? $request->all() : $request->except('password') ;
        
        if(array_key_exists('password',  $input)){
            $input['password'] = Hash::make($input['password']);
        }
       // return $input;
        $user =  $user->update($input);;

        Flash::success('User updated successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }

        $user->delete();

        Flash::success('User deleted successfully.');
        return redirect(route('users.index'));
    }

    public function getArrayKey()
    {

    }
}
