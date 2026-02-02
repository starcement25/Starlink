<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Reward;
use App\Models\Branch;
use App\Models\Lifting;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Traits\HelperTrait;
use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\RejectedRedeemtion;
use Carbon\Carbon;
use App\Models\UserCatalogueRedeemtion;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('users.view');
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('users.create');
        $maritialStatus = ['' => 'Select Maritial Status', '1'=> 'Married', '2'=> 'Un Married'];
        $rolesArr  = Role::select('id', 'role_name')->where('is_reserved_role',0)->pluck('role_name', 'id')->toArray();
       
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches =  $branchArr ;
        $roles    = ['' => 'Select Role'] + $rolesArr ;
        $statusOption = ['1'=> 'Active', '0'=> 'Inactive'];
        
        return view('admin.user.create')
                ->with('maritialStatusOption', $maritialStatus)->with('maritialStatus', "")
                ->with('roleOption', $roles)->with('roleSelected', "")
                ->with('branchOption', $branches)->with('branchSelected', "") 
                ->with('statusOption', $statusOption)->with('statusSelected', "");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('users.create');
        // if($request->role == '3'){
        //   $validated =   $request->validate(['aadhaar_no' => 'required']) ;
        // }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $allocted_branches=[];
        foreach($input['allocated_branches'] as $val)
        {
            if($val!=null)
            {
                array_push($allocted_branches,$val);
            }
        }
        $input['allocated_branches']=json_encode($allocted_branches);
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('users.view');
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('users.edit');
        $user = User::find($id);
        //return explode(',',$user->allocated_branches);
        $maritialStatus = ['' => 'Select Status', '1'=> 'Married', '2'=> 'Un Married'];
        $rolesArr  = Role::select('id', 'role_name')->where('is_reserved_role',0)->pluck('role_name', 'id')->toArray();
        $roles    = ['' => 'Select Role'] + $rolesArr ;
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches =  $branchArr ;
        $statusOption = ['1'=> 'Active', '0'=> 'Inactive'];
       
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }
        return view('admin.user.edit')
                ->with('user', $user)
                ->with('maritialStatusOption', $maritialStatus)->with('maritialStatus', $user->marital_status)
                ->with('roleOption', $roles)->with('roleSelected', $user->role)
                ->with('branchOption', $branches)->with('branchSelected', json_decode($user->allocated_branches)) 
                ->with('statusOption', $statusOption)->with('statusSelected', $user->status);
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('users.edit');
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
        $allocted_branches=[];
        foreach($input['allocated_branches'] as $val)
        {
            if($val!=null)
            {
                array_push($allocted_branches,$val);
            }
        }
        $input['allocated_branches']=json_encode($allocted_branches);
       // return $input;
        $user =  $user->update($input);

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
        \Helper::checkIsUserAuthorizeToPerformTheTask('users.delete');
        $user = User::find($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }

        $user->delete();

        Flash::success('User deleted successfully.');
        return redirect(route('users.index'));
    }

    public function updateUserNetPoint() 
    {
        return "Function blocked by Dev.";
        //----------------------finding mason ids
            $userIds = User::where('role',2)->pluck('id')->toArray();
            $ids=[];
            foreach($userIds as $userId)
            {
                $rewardPoint        = Reward::selectRaw("IFNULL(SUM(point), 0) AS `point`")->where('user_id', $userId)
                                    ->where('is_verified', 1)->value('point') ;
            
                $redeemedPoint      =   UserCatalogueRedeemtion::selectRaw("IFNULL(SUM(redeemed_point), 0) AS `redeemed_point`")
                                    ->where('user_id', $userId)->value('redeemed_point') ;
            
                $rejectionCreditPoint = RejectedRedeemtion::selectRaw("IFNULL(SUM(point_credited), 0) AS `point_credited`")
                                    ->where('user_id', $userId)->value('point_credited') ;

                $points = ($rewardPoint - $redeemedPoint + $rejectionCreditPoint) ;

                $netPoint = User::where('id',$userId)->pluck('points')->toArray()[0];
                if($points != $netPoint)
                {
                    $ids[]=$userId;
                }
            }
            return $ids;
        //--------------------------------------------update points
            // $masonIds =[31346,31466,31586,31596,31637,31656,31657,31795,31798,31826,31834,31841,31886,31931,32088,32185,32204,32212,32248,32322,32357,32433,32497,32501,32502,32557,32601,32605,32612,32682,32714,32808,32809,32830,32838,32846,32860,32876,32882,32887,32917,32989,33016,33118,33150,33153,33156,33172,33178,33195,33341,33367,33377,33404,33441,33494,33530,33591,33612,33641,33683,33703,33923,33972,34084,34171,34306,34308,34317,34341,34348,34383,34385,34424,34425,34428,34432,34435,34618,34620,34621,34642,34661,34680,34701,34841,34879,34882,34925,34964,35095,35106,35136,35146,35148,35182,35211,35216,35643];
            // foreach($masonIds as $masonId)
            // {
            //     //for update the pointsof mason 
            //     $this->updatePoint($masonId);
            // }
            // return "updated";
    }
    public function cleanDbRecords()
    {
        if(\Auth::user()->role == 5)
        {
            // $testMasonIds = User::where("name", "LIKE", "%test%")->where("role", 2)->pluck("id")->toArray();
            // foreach($testMasonIds as $testMasonId)
            // {
            //     $this->updatePoint($testMasonId);
            // }
            // $testLiftingIds = Reward::whereIn("user_id", $testMasonIds)->where("is_bonus", 0)->whereNotNull("lifting_id")->pluck("lifting_id")->toArray();
            // $liftings = Lifting::whereIn('id', $testLiftingIds)->get();
            // foreach($liftings as $lifting)
            // {
            //     $lifting->delete();
            // }
            // return "mason point updated successfully.";
            return "Function blocked by Dev.";
        }
        else
        {
            return "Only admin have this permission.";
        }
    }

    public function searchUser(Request $request)
    {
        $teLists = User::select("id", "name", "phone");
        if($request->has('user_role') && !empty($request->user_role))
        {
            $teLists = $teLists->whereIn('role', [$request->user_role]);
        }
        if($request->has('ignore') && !empty($request->ignore))
        {
            $teLists = $teLists->whereNot('id', [$request->ignore]);
        }
        if($request->has('searchVal') && !empty($request->searchVal))
        {
            $teLists = $teLists->where('name', 'LIKE', '%' . $request->searchVal . '%')->orWhere('phone', 'LIKE', '%' . $request->searchVal . '%')->get();
        }
        $teLists = $teLists->get();
        return response()->json([
            'status' => true, 'data' => [
                "teLists" => $teLists
            ]
        ], 200);
    }
    public function getTeMasons($teID)
    {
        $masonLists = User::select("id", "name", "phone")->where("parent", $teID)->get();
        return response()->json([
            'status' => true, 'data' => [
                "masonLists" => $masonLists
            ]
        ], 200);
    }

   public function removeTestingAccountAndData()
   {
        try
        {
            return "Service blocked By Dev.";
            $userPhoneNumbers = [
                "7074074395"
            ];
            foreach($userPhoneNumbers as $userPhoneNumber)
            {
                $user = User::where("phone", $userPhoneNumber)->first();
                if(empty($user))
                {
                    continue;
                }
                DB::beginTransaction();
                DB::table('branch')->where("asm_user_id", $user->id)->delete();
                DB::table('customer_liftings')->where("dealer_id", $user->id)->delete();
                DB::table('dealer_linkage_request')->where("user_id", $user->id)->delete();
                DB::table('dealer_linkage_request')->where("dealer_id", $user->id)->delete();
                DB::table('dealer_linkage_request')->where("action_taken_by", $user->id)->delete();
                DB::table('dealer_linkage_request_history')->where("user_id", $user->id)->delete();
                DB::table('dealer_linkage_request_history')->where("dealer_id", $user->id)->delete();
                DB::table('dealer_linkage_request_history')->where("action_taken_by", $user->id)->delete();
                DB::table('employee_branches')->where("user_id", $user->id)->delete();
                DB::table('lifting')->where("req_by", $user->id)->delete();
                DB::table('lifting')->where("action_taken_by", $user->id)->delete();
                DB::table('lifting')->where("user_id", $user->id)->delete();
                DB::table('lifting_approval_history')->where("seek_approval_by", $user->id)->delete();
                DB::table('lifting_approval_history')->where("action_taken_by", $user->id)->delete();
                DB::table('logs')->where("user_id", $user->id)->delete();
                DB::table('mason_dealers')->where("mason_id", $user->id)->delete(); 
                DB::table('mason_dealers')->where("dealer_id", $user->id)->delete();
                DB::table('mason_lifting')->where("mason_id", $user->id)->delete();
                DB::table('notifications')->where("notifiable_id", $user->id)->delete();
                DB::table('oauth_access_tokens')->where("user_id", $user->id)->delete();
                DB::table('rejected_redeemtions')->where("user_id", $user->id)->delete();
                DB::table('rewards')->where("user_id", $user->id)->delete();
                DB::table('rewards')->where("verified_by", $user->id)->delete();
                DB::table('reward_history')->where("user_id", $user->id)->delete();
                DB::table('reward_history')->where("verified_by", $user->id)->delete();
                $user_catalogue_redeemtions_ids = DB::table('user_catalogue_redeemtions')->where("user_id", $user->id)->pluck("id")->toArray();
                DB::table('supports')->whereIn("order_id", $user_catalogue_redeemtions_ids)->delete();
                DB::table('user_catalogue_redeemtions')->where("user_id", $user->id)->delete();
                DB::table('user_disable_history')->where("user_id", $user->id)->delete();
                DB::table('users')->where("id", $user->id)->delete();
                DB::commit();
            }
            return "Done";
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return $e->getMessage();
        }
   }

   public function adjustingMasonNegativePoints()
   {
    return "Service Block by Dev";
    try {
        $usersWithNegativePoints = User::where("role", User::ROLE_MASON)->where("points", "<", "0")->select("id", "points")->get();
        DB::beginTransaction();
        foreach($usersWithNegativePoints as $usersWithNegativePoint)
        {
            // Reward::create([
            //     "point" => $usersWithNegativePoint->points,
            //     "user_id" => $usersWithNegativePoint->id,
            //     "is_verified" => Reward::VERIFIED,
            //     "is_eligible_for_ledger" => Reward::ELIGIBLE_FOR_LEDGER_YES,
            //     "description" => "Adjusting negative point.",
            //     "remarks" => "Adjusting negative point.",
            // ]);

            // $this->updatePoint($usersWithNegativePoint->id);
        }
        DB::commit();
        return "ok";

    } catch (\Exception $exception) {
        DB::rollback();
        return $e->getMessage();
    }
   }
}
