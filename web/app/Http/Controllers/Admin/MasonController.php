<?php

namespace App\Http\Controllers\Admin;

use Flash;
use App\Models\UserDisableHistory;
use App\Models\User;
use App\Models\State;
use App\Utils\Helper;
use App\Models\Branch;
use Carbon\Carbon;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\MasonDealer;
use App\Traits\HelperTrait;
use App\Exports\MasonExport;
use Illuminate\Http\Request;
use App\DataTables\MasonDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\MasonRepository;
use App\DataTables\MasonPointDataTable;
use App\Models\UserCatalogueRedeemtion;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Mason\CreateMasonRequest;
use App\Http\Requests\Mason\UpdateMasonRequest;
use App\Models\RejectedRedeemtion;
use App\Http\Requests\Mason\PointManupulateRequest;
use App\DataTables\MasonLedgerDataTable;

class MasonController extends AppBaseController
{
    use HelperTrait{
        uploadAadhaarDoc as traitUploadAadhaarDoc; // Alias the trait method
    }
    /** @var MasonRepository $masonRepository*/
    private $masonRepository;

    public function __construct(MasonRepository $masonRepo)
    {
        $this->masonRepository = $masonRepo;
    }

    /**
     * Display a listing of the Mason.
     */
    public function index(MasonDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.view') ;
        $filterBy = $request->filter_by;
        $fromDataVal = $request->fromDate;
        $toDataVal = $request->toDate;
        $statusFilter = $request->status;
        if($request->has('fromDate', 'toDate', 'filter_by'))
        {
            if( !empty($request->filter_by) && (empty($request->fromDate) || empty($request->toDate)) )
            {
                Flash::error('Please Select Start and End Date.'); 
                // return $dataTable->render('admin.masons.index');
            }
            if((!empty($request->fromDate) || !empty($request->toDate)) && empty($request->filter_by))
            {
                Flash::error('Please Select Filter By option.'); 
                // return $dataTable->render('admin.masons.index');
            }
            if( !empty($request->fromDate) && !empty($request->toDate) && $request->fromDate > $request->toDate)
            {
                Flash::error('From date cannot be greater than To date.'); 
                // return $dataTable->render('admin.masons.index', [
                //     'fromDataVal' => $fromDataVal,
                //     'toDataVal' => $toDataVal,
                // ]);
            }

        }
        // return $request->fromDate;
        return $dataTable->with([
            'fromDate' => $request->fromDate,
            'toDate' => $request->toDate,
            'filterBy' => $filterBy,
            'statusFilter' => $statusFilter,
        ])->render('admin.masons.index', [
            'fromDataVal' => $fromDataVal,
            'toDataVal' => $toDataVal,
            'filterBy' => $filterBy,
            'statusFilter' => $statusFilter,
        ]);
                
    }

    /**
     * Show the form for creating a new Mason.
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.create') ;
        $status = ['' => 'Select Status', '1'=> 'Married', '2'=> 'Un Married'];
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches = ['' => 'Select Branch'] + $branchArr ;
        $users = User::select('id', 'name')->where('status', '1')->whereIn('role', ['1'])->pluck('name', 'id')->toArray();
        $users = ['' => 'Select User'] + $users ;

        $dealerArr = User::select('id', 'name')->whereIn('role', ['3','4'])->pluck('name', 'id')->toArray();
        $dealers = ['' => 'Select User'] + $dealerArr ;

        $stateArr = State::pluck('state_name', 'id')->toArray();
        $states = ['' => 'Select State'] + $stateArr ;


        // return view('admin.masons.create')
        //         ->with('status', "")->with('maritalStatus', "")
        //         ->with('usersOption', $users)->with('userSelected', "")
        //         ->with('branchOption', $branches)->with('branchSelected', "") 
        //         ->with('dealerOption', $dealers)->with('dealerSelected', "") 
        //         ->with('stateOption', $states)->with('stateSelected', "");
      //  return view('admin.masons.create');
    }

    /**
     * Store a newly created Mason in storage.
     */
    public function store(CreateMasonRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.create') ;
        $input = $request->except(['dealers', 'aadhar_img']);
        $input['role'] = 2 ;
        $mason = User::create($input);
        if($request->has('aadhaar_img')){
            // $data = $this->uploadFile($request->file('aadhar_img'), 'aadhar') ;
            $file = $request->file('aadhar_img');
            $filename = "M".$mason->id.".".$request->file('aadhar_img')->getClientOriginalExtension();
            // $location = base_path().'/public/aadhaar';
            $location = '/var/www/html/public/aadhaar';
            $file->move($location,$filename);
            $mason->update(['aadhaar_doc' => $filename]) ;
        }
        
        $this->addMasonDealers($mason->id, $request->dealers) ;

        Flash::success('Mason saved successfully.'); 

        return redirect(route('masons.index'));
    }

    /**
     * Display the specified Mason.
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.view') ;
        $mason = User::find($id);

        if (empty($mason)) {
            Flash::error('Mason not found');

            return redirect(route('masons.index'));
        }

        return view('admin.masons.show')->with('mason', $mason);
    }

    /**
     * Show the form for editing the specified Mason.
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.edit') ;
        $mason = User::find($id);

        if (empty($mason)) {
            Flash::error('Mason not found');

            return redirect(route('masons.index'));
        }
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches = ['' => 'Select Branch'] + $branchArr ;
        $users = User::select('id', 'name')->where('status', '1')->whereIn('role', ['1'])->pluck('name', 'id')->toArray();
        $users = ['' => 'Select User'] + $users ;

        $dealerArr = User::select('id', 'name')->whereIn('role', ['3','4','6'])->where('branch_id',$mason->branch_id)->pluck('name', 'id')->toArray();
        $dealers =  $dealerArr ;

        $dealersSelected = $mason->mason_dealers->pluck('dealer_id')->toArray() ;
        $stateArr = State::pluck('state_name', 'id')->toArray();
        $states = ['' => 'Select State'] + $stateArr ;

        return view('admin.masons.edit')->with('mason', $mason)
                ->with('status', $mason->status)->with('maritalStatus', $mason->marital_status)
                ->with('usersOption', $users)->with('userSelected', $mason->parent)
                ->with('branchOption', $branches)->with('branchSelected', $mason->branch_id) 
                ->with('dealerOption', $dealers)->with('dealerSelected', $dealersSelected) 
                ->with('stateOption', $states)->with('stateSelected', $mason->state);;
    }

    /**
     * Update the specified Mason in storage.
     */
    public function update($id, UpdateMasonRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.edit') ;
        //return public_path();
        $mason = User::find($id);
        $masonOldData = $mason->replicate();

        if (empty($mason)) {
            Flash::error('Mason not found');

            return redirect(route('masons.index'));
        }
        // $input  = $request->except(['img']) ;
        $input = !empty($request->password) ? $request->except(['aadhar_img', 'dealers']) : $request->except(['password', 'aadhar_img', 'dealers']) ;
        
        if(array_key_exists('password',  $input)){
            $input['password'] = Hash::make($input['password']);
        }
       $mason->update($input);

       // If status is disable keep diable history
       if($request->status != 1 && ( $masonOldData->status == 1 || $masonOldData->disable_reason != $request->disable_reason ) )
       {
        $mason->update(["disable_date_time" => Carbon::now()]);
        UserDisableHistory::create([
            "user_id" => $id,
            "disable_date_time" => Carbon::now(),
            "disable_reason" => $request->disable_reason,
        ]);
       }
         
        // Update Image If There Is Image.
         if(!empty($request->aadhar_img)){
            if(!empty($mason->aadhaar_doc)){
                if(file_exists('/var/www/html/public/aadhaar/'.$mason->aadhaar_doc)){
                    unlink('/var/www/html/public/aadhaar/'.$mason->aadhaar_doc);
                }
            }
            
            // $data = $this->traitUploadAadhaarDoc($request->file('aadhar_img')) ;
            $file = $request->file('aadhar_img');
            $filename = "M".$mason->id.".".$request->file('aadhar_img')->getClientOriginalExtension();
            // $location = base_path().'/public/aadhaar';
            $location = '/var/www/html/public/aadhaar';
            $file->move($location,$filename);
            $mason->update(['aadhaar_doc' => $filename]) ;
        }

        MasonDealer::whereIn('id', $mason->mason_dealers->pluck('id')->toArray())->delete();
        $this->addMasonDealers($mason->id, $request->dealers) ;

        Flash::success('Mason updated successfully.');

        return redirect(route('masons.index'));
    }

    /**
     * Remove the specified Mason from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.delete') ;
        $mason = User::find($id);

        if (empty($mason)) {
            Flash::error('Mason not found');

            return redirect(route('masons.index'));
        }

        $mason->delete($id);
        Flash::success('Mason deleted successfully.');

        return redirect(route('masons.index'));
    }

    // Point Add & Deduct For Mason
    public function saveManupulation(PointManupulateRequest $request)
    { 
        try
        {
            \Helper::checkIsUserAuthorizeToPerformTheTask('list.edit') ;
            // 1 = Add , 2 = Deduct
            // $request->all();
            $user = \Helper::getUser($request->user);
            $input = [] ;
            $type  = $model = $tableId = $response = '' ;

            //return $user;
            if(($user->points < $request->point) && $request->type == 2 && \Auth::user()->role != 5)
            {
                Flash::error("Deduct point can't be greater than net point");
                return redirect()->back();
            }
            $action = ['1' => 'added', '2'=> 'deducted'];
            \DB::beginTransaction();
            if($request->type == 1){

                 $input = [
                    'user_id' => $request->user,
                    'point' => $request->point,
                    'is_verified' => 1,
                    'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_YES,
                    // 'description' => 'Point add',
                    'description' => $request->description,
                    'remarks' => $request->remarks,
                ];
                $type  = 'Point Added' ;
                $model = 'Reward' ;
                
                $reward   = Reward::create($input);
                $tableId  = $reward->id ;
                $response = $reward ;

                // Reward::create([
                //     'user_id' => $request->user,
                //     'point' => $request->point,
                //     'is_verified' => 1,
                //     'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_YES,
                //     // 'description' => 'Point add',
                //     'description' => $request->description,
                //     'remarks' => $request->remarks,
                // ]);
            }else if($request->type == 2){

                $input = [
                    'user_id'       => $request->user,
                    'redeemed_point' => $request->point,
                    // 'description' => 'Point deduct by admin',
                    'description' => $request->description,
                    'remarks' => $request->remarks,
                ] ; 
                
                $redeemtion = UserCatalogueRedeemtion::create($input);

                $model = 'UserCatalogueRedeemtion';
                $type = 'Point Subtracted' ;
                $tableId = $redeemtion->id ;
                $response = $redeemtion ;

                if($user->status == 1 && $request->user_disable == 1) // means user disable true
                {
                    User::where("id", $request->user)->update([
                        "status" => 0,
                        "disable_date_time" => Carbon::now(),
                        "disable_reason" => $request->description,
                    ]);
                    UserDisableHistory::create([
                        "user_id" => $request->user,
                        "disable_date_time" => Carbon::now(),
                        "disable_reason" => $request->description,
                        "point_deducted" => $request->point,
                    ]);
                }
            }
            // Update total user point.
            $this->updatePoint($request->user);

            // Log Entry.
            $logData = [
                'table_id' => $tableId,
                'user_id' => \Auth::user()?->id,
                'model_name' => $model,
                'request'=> json_encode($input) ,
                'response'=> json_encode($response) ,
                'action' => 'create',
                'remarks'=> $type,
            ];

            // Create Log.
            $this->createLog($logData) ;


            \DB::commit();

            Flash::success('Mason point '.$action[$request->type].' successfully.');
            return redirect(route('point.list')) ;
        }
        catch(\Exception $e)
        {
            \DB::rollback();
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }

    // All Masons Point List 
    public function showMasonsPoint(MasonPointDataTable $dataTable)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('list.view') ;
       return  $dataTable->render('admin.masons.point-list');
    }

    // Show manupulate Form.
    public function showManupulateForm(Request $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('list.edit') ;
        $user = User::with('by_created')->where('id', $id)->where('role', 2)->first();
        if(empty($user)){
            abort(404);
        }
       return view('admin.masons.point-manupulate', ['user'=> $user, 'userId'=> $id]) ;
    }

    public function addMasonDealers($masonId, $dealers)
    {
        if(count($dealers) > 0){
            foreach ($dealers as $key => $dealer) {
                MasonDealer::create([
                    'mason_id' => $masonId ,
                    'dealer_id' => $dealer ,
                ]);
            }
        }
    }
    


    public function showLedger(MasonLedgerDataTable $dataTable, Request $request)
    {
        // return "service unavailable by dev";
        $selectedMason = $request->mason;
        $errorMsg = [];
        $selectedDateFrom = $request->date_from;
        $selectedDateTo = $request->date_to;
        if($request->has('date_from') && !empty($request->date_from) && (!$request->has('date_to') || empty($request->date_to)))
        {
            $errorMsg['date_to'] = "To date is required.";
        }
        elseif($request->has('date_to') && !empty($request->date_to) && (!$request->has('date_from') || empty($request->date_from)))
        {
            $errorMsg['date_from'] = "From date is required.";
        }

        if(!empty($selectedDateFrom) && !empty($selectedDateTo))
        {
            if($selectedDateFrom > $selectedDateTo)
            {
                $tempDate = $selectedDateFrom;
                $selectedDateFrom = $selectedDateTo;
                $selectedDateTo = $tempDate;
            }
        }
        
        // $loggedUser=Auth::user();
        // if($loggedUser->role > 6)
        // {
        //     $allocated_branches=json_decode($loggedUser->allocated_branches);
        //     $users    = User::where('role', 2)->whereIn('branch_id',$allocated_branches)->orderBy('name', 'DESC')->get();
        // }
        // else
        // {
        //     $users    = User::where('role', 2)->orderBy('name', 'DESC')->get();
        // }
        return $dataTable->with([
            'user' => base64_decode($request->mason),
            'selectedDateFrom' => $selectedDateFrom,
            'selectedDateTo' => $selectedDateTo,
        ])->render('admin.ledger.index',[
            // 'users' => $users,
            'selectedMason' => $selectedMason,
            'errorMsg' => $errorMsg,
            'selectedDateFrom' => $selectedDateFrom,
            'selectedDateTo' => $selectedDateTo,
        ]);
        // return view('admin.ledger.bkp_index')->with('users', $users);
    }

    public function getMasonDropDownOptions(Request $request)
    {
        try {
            if(\Auth::check())
            {
                $search = $request->input('q'); // search term
                $page = $request->input('page', 1); // current page, default = 1
                $page = $page < 1 ? 1 : $page;
                $perPage = 50; // items per page
                $path = $request->input('path') ?? "";

                $loggedUser=Auth::user();
                if($loggedUser->role > 6)
                {
                    $allocated_branches=json_decode($loggedUser->allocated_branches);
                    $users    = User::query()->where('role', 2)->whereIn('branch_id',$allocated_branches)->orderBy('name', 'DESC');
                }
                else
                {
                    $users    = User::query()->where('role', 2)->orderBy('name', 'DESC');
                }

                if ($search) {
                    $users->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('id', 'like', "%" . base64_decode($search) . "%");
                }

                $total = $users->count();
                $options = $users->skip(($page - 1) * $perPage)
                                ->take($perPage)
                                ->selectRaw('TO_BASE64(id) AS encoded_id, CONCAT(name, " - ", phone) AS text')
                                ->get();
                
                // Add custom option only on first page
                if (!$search && $page == 1 && $path == "admin/verify/lifting") {
                    $allOption = collect([['encoded_id' => base64_encode('ALL'), 'text' => 'ALL']]);
                    $options = $allOption->merge($options);
                }

                return response()->json([
                    'items' => $options,
                    'more' => ($page * $perPage) < $total,
                    "msg" => "Data fetched successfully.",
                    "detail_error" => null,
                ]);
            }
            else
            {
                throw new \Exception("Login Require.");
            }
        } catch (\Throwable $th) {
            return response()->json([
                "items" => [],
                "more" => false,
                "msg" => "Service is unavilable.",
                "detail_error" => $th->getMessage(),
            ]);
        }
    }
    
    // Previous Function 
    // public function exportLedger_PREV(Request $request)
    // {
    //     $loggedUser=Auth::user();
    //     $mason = base64_decode($request->mason);
    //     set_time_limit(0);
    //     $limit = 10;
    //     $offset = 0;
    //     $count = 0;
    //     $filename = "Point_Ledger_".$this->getUniqueId().".csv";
    //     $headings = [
    //         "Lifting Date",
    //         "Creation Date",
    //         "Order No",
    //         "Name",
    //         "Phone No.",
    //         "Branch",
    //         "BDE Code",
    //         "BDE Name",
    //         "Description",
    //         "Credit Point",
    //         "Debit Point",
    //     ];

    //     $myfile = fopen(public_path("/excel_exports/ledger_points/").$filename, "w");
    //     fputcsv($myfile,$headings);
    //     while(true)
    //     {
    //         if(($request->has('date_from') && !empty($request->date_from)) && ($request->has('date_to') && !empty($request->date_to)))
    //         {
    //             if($mason == "ALL"){
    //                 if($loggedUser->role > 6)
    //                 {
    //                     $allocated_branches=json_decode($loggedUser->allocated_branches);
    //                     $userIds=implode(",",User::whereIn('branch_id',$allocated_branches)->pluck('id')->toArray());
    //                     // $data = DB::select("SELECT * FROM (
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`
    //                     //     FROM `user_catalogue_redeemtions` WHERE `user_id` IN (".$userIds.")
    //                     //     UNION ALL
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
    //                     //     FROM `rewards` WHERE `user_id` IN (".$userIds.") AND`is_verified`='1'
    //                     //     UNION ALL
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
    //                     //     FROM `rejected_redeemtions` WHERE `user_id` IN (".$userIds.")
    //                     //     )P
    //                     //     ORDER BY `created_at`"); -- By Subhajit Da
    //                     $data = DB::select("SELECT * FROM (
    //                         SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `user_catalogue_redeemtions` WHERE `user_id` IN (".$userIds.") AND created_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59'
    //                         UNION ALL
    //                         SELECT get_mason_details(`rewards`.`user_id`) AS mason_details,'',`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
    //                         rewards.updated_at AS ledger_date, `lifting`.`lifting_date` FROM `rewards` LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `rewards`.`user_id` IN (".$userIds.") AND rewards.updated_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59' AND `rewards`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`reward_history`.`user_id`) AS mason_details,'',`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
    //                         reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date` FROM `reward_history` LEFT JOIN rewards ON rewards.id = reward_history.reward_id LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `reward_history`.`user_id` IN (".$userIds.") AND reward_history.reward_date_time BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59' AND `reward_history`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `rejected_redeemtions` WHERE `user_id` IN (".$userIds.") AND created_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59'
    //                         )P
    //                         ORDER BY ledger_date");
    //                 }
    //                 else
    //                 {
    //                     // $data = DB::select("SELECT * FROM (
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`
    //                     //     FROM `user_catalogue_redeemtions`
    //                     //     UNION ALL
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
    //                     //     FROM `rewards` WHERE `is_verified`='1'
    //                     //     UNION ALL
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
    //                     //     FROM `rejected_redeemtions`
    //                     //     )P
    //                     //     ORDER BY `created_at` LIMIT ?  OFFSET ?", [$limit, $offset]);
    //                     $data = DB::select("SELECT * FROM (
    //                         SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `user_catalogue_redeemtions` WHERE created_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59'
    //                         UNION ALL
    //                         SELECT get_mason_details(`rewards`.`user_id`) AS mason_details,'',`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
    //                         rewards.updated_at AS ledger_date, `lifting`.`lifting_date` FROM `rewards` LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `rewards`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."' AND rewards.updated_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59'
    //                         UNION ALL
    //                         SELECT get_mason_details(`reward_history`.`user_id`) AS mason_details,'',`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
    //                         reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date` FROM `reward_history` LEFT JOIN rewards ON rewards.id = reward_history.reward_id LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `reward_history`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."' AND reward_history.reward_date_time BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59'
    //                         UNION ALL
    //                         SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `rejected_redeemtions` WHERE created_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59'
    //                         )P
    //                         ORDER BY ledger_date LIMIT ?  OFFSET ?", [$limit, $offset]);
    //                 }

    //             }else{
    //                 // $data = DB::select("SELECT * FROM (
    //                 //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`
    //                 //     FROM `user_catalogue_redeemtions` WHERE `user_id`= ?
    //                 //     UNION ALL
    //                 //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
    //                 //     FROM `rewards` WHERE `user_id`=?
    //                 //     AND `is_verified`='1'
    //                 //     UNION ALL
    //                 //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
    //                 //     FROM `rejected_redeemtions` WHERE `user_id`=?
    //                 //     )P
    //                 //     ORDER BY `created_at` LIMIT ?  OFFSET ?", [$mason, $mason, $mason, $limit, $offset]);
    //                 $data = DB::select("SELECT * FROM (
    //                         SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `user_catalogue_redeemtions` WHERE `user_id`= ? AND created_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59'
    //                         UNION ALL
    //                         SELECT get_mason_details(`rewards`.`user_id`) AS mason_details,'',`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
    //                         rewards.updated_at AS ledger_date, `lifting`.`lifting_date` FROM `rewards` LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `rewards`.`user_id` =? AND rewards.updated_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59' AND `rewards`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`reward_history`.`user_id`) AS mason_details,'',`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
    //                         reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date` FROM `reward_history` LEFT JOIN rewards ON rewards.id = reward_history.reward_id LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `reward_history`.`user_id` =? AND reward_history.reward_date_time BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59' AND `reward_history`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `rejected_redeemtions` WHERE `user_id`=? AND created_at BETWEEN '".$request->date_from." 00:00:00' AND '".$request->date_to." 23:59:59'
    //                         )P
    //                         ORDER BY ledger_date LIMIT ?  OFFSET ?", [$mason, $mason, $mason, $mason, $limit, $offset]);
    //             }
    //         }
    //         else
    //         {
    //             if($mason == "ALL"){
    //                 if($loggedUser->role > 6)
    //                 {
    //                     $allocated_branches=json_decode($loggedUser->allocated_branches);
    //                     $userIds=implode(",",User::whereIn('branch_id',$allocated_branches)->pluck('id')->toArray());
    //                     // $data = DB::select("SELECT * FROM (
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`
    //                     //     FROM `user_catalogue_redeemtions` WHERE `user_id` IN (".$userIds.")
    //                     //     UNION ALL
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
    //                     //     FROM `rewards` WHERE `user_id` IN (".$userIds.") AND`is_verified`='1'
    //                     //     UNION ALL
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
    //                     //     FROM `rejected_redeemtions` WHERE `user_id` IN (".$userIds.")
    //                     //     )P
    //                     //     ORDER BY `created_at`"); -- By Subhajit Da
    //                     $data = DB::select("SELECT * FROM (
    //                         SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `user_catalogue_redeemtions` WHERE `user_id` IN (".$userIds.")
    //                         UNION ALL
    //                         SELECT get_mason_details(`rewards`.`user_id`) AS mason_details,'',`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
    //                         rewards.updated_at AS ledger_date, `lifting`.`lifting_date` FROM `rewards` LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `rewards`.`user_id` IN (".$userIds.") AND `rewards`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`reward_history`.`user_id`) AS mason_details,'',`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
    //                         reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date` FROM `reward_history` LEFT JOIN rewards ON rewards.id = reward_history.reward_id LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `reward_history`.`user_id` IN (".$userIds.") AND `reward_history`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `rejected_redeemtions` WHERE `user_id` IN (".$userIds.")
    //                         )P
    //                         ORDER BY ledger_date");
    //                 }
    //                 else
    //                 {
    //                     // $data = DB::select("SELECT * FROM (
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`
    //                     //     FROM `user_catalogue_redeemtions`
    //                     //     UNION ALL
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
    //                     //     FROM `rewards` WHERE `is_verified`='1'
    //                     //     UNION ALL
    //                     //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
    //                     //     FROM `rejected_redeemtions`
    //                     //     )P
    //                     //     ORDER BY `created_at` LIMIT ?  OFFSET ?", [$limit, $offset]);
    //                     $data = DB::select("SELECT * FROM (
    //                         SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `user_catalogue_redeemtions`
    //                         UNION ALL
    //                         SELECT get_mason_details(`rewards`.`user_id`) AS mason_details,'',`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
    //                         rewards.updated_at AS ledger_date, `lifting`.`lifting_date` FROM `rewards` LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `rewards`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`reward_history`.`user_id`) AS mason_details,'',`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
    //                         reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date` FROM `reward_history` LEFT JOIN rewards ON rewards.id = reward_history.reward_id LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `reward_history`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `rejected_redeemtions`
    //                         )P
    //                         ORDER BY ledger_date LIMIT ?  OFFSET ?", [$limit, $offset]);
    //                 }

    //             }else{
    //                 // $data = DB::select("SELECT * FROM (
    //                 //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`
    //                 //     FROM `user_catalogue_redeemtions` WHERE `user_id`= ?
    //                 //     UNION ALL
    //                 //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
    //                 //     FROM `rewards` WHERE `user_id`=?
    //                 //     AND `is_verified`='1'
    //                 //     UNION ALL
    //                 //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
    //                 //     FROM `rejected_redeemtions` WHERE `user_id`=?
    //                 //     )P
    //                 //     ORDER BY `created_at` LIMIT ?  OFFSET ?", [$mason, $mason, $mason, $limit, $offset]);
    //                 $data = DB::select("SELECT * FROM (
    //                         SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `user_catalogue_redeemtions` WHERE `user_id`= ?
    //                         UNION ALL
    //                         SELECT get_mason_details(`rewards`.`user_id`) AS mason_details,'',`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
    //                         rewards.updated_at AS ledger_date, `lifting`.`lifting_date` FROM `rewards` LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `rewards`.`user_id` =? AND `rewards`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`reward_history`.`user_id`) AS mason_details,'',`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
    //                         reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date` FROM `reward_history` LEFT JOIN rewards ON rewards.id = reward_history.reward_id LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `reward_history`.`user_id` =? AND `reward_history`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
    //                         UNION ALL
    //                         SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
    //                         created_at AS ledger_date, NULL AS `lifting_date` FROM `rejected_redeemtions` WHERE `user_id`=?
    //                         )P
    //                         ORDER BY ledger_date LIMIT ?  OFFSET ?", [$mason, $mason, $mason, $mason, $limit, $offset]);
    //             }
    //         }
    //         foreach($data as $val)
    //         {
    //             $mason_details = json_decode($val->mason_details);
    //             $content = [
    //                 !empty($val->lifting_date) ? Carbon::parse($val->lifting_date)->toDateString() : "N/A",
    //                 Carbon::parse($val->created_at)->toDateString(),
    //                 $val->order_id,
    //                 $mason_details == null ? "" : $mason_details->name,
    //                 $mason_details == null ? "" : $mason_details->phone,
    //                 $mason_details == null ? "" : ($mason_details->branch == null ? "" : $mason_details->branch->name),
    //                 $mason_details == null ? "" : ($mason_details->te == null ? "" : $mason_details->te->code),
    //                 $mason_details == null ? "" : ($mason_details->te == null ? "" : $mason_details->te->name),
    //                 $val->description,
    //                 $val->credit_point,
    //                 $val->debit_point,
    //             ];
    //             fputcsv($myfile,$content);
    //         }
    //         if(count($data) < $limit)
    //         {
    //             $count += count($data);
    //             break;
    //         }
    //         $count += $limit;
    //         $offset += $limit;
    //     }
    //     fclose($myfile);
    //     $filePath = public_path("/excel_exports/ledger_points/".$filename);
    //     return response()->download($filePath)->deleteFileAfterSend(true);
    // }

    // Export Function By User
    public function exportLedger(Request $request){
        $loggedUser=Auth::user();
        $mason = base64_decode($request->mason);
        set_time_limit(0);
        $limit = 10;
        $offset = 0;
        $count = 0;
        $filename = "Point_Ledger_".$this->getUniqueId().".csv";
        // $headings = [
        //     "Lifting Date",
        //     "Creation Date",
        //     "Order No",
        //     "Name",
        //     "Phone No.",
        //     "Branch",
        //     "BDE Code",
        //     "BDE Name",
        //     "Description",
        //     "Credit Point",
        //     "Debit Point",
        //     "Remaining Balance",

        // ];

        $headings = [
            "Lifting Date",
            "Creation Date",
            "Order No",
            "Name",
            "Phone No.",
            "Branch",
            "BDE Code",
            "BDE Name",
            "Address",
            "Description",
            "Credit Point",
            "Catalogue Point",
            "TDS Point",
            "Debit Point",
            "Remaining Balance",
        ];

         // Determine allowed user IDs
        if ($mason === "ALL" && $loggedUser->role > 6) {
            $allocated = json_decode($loggedUser->allocated_branches);
            $userIds = User::whereIn('branch_id', $allocated)->pluck('id')->toArray();
        } elseif ($mason === "ALL") {
            $userIds = User::pluck('id')->toArray();
        } else {
            $userIds = [$mason];
        }

        // Prevent empty IN ()
        

        $idList = implode(",", $userIds);

        if (empty($idList)) {
            $sql =  "(SELECT * FROM (SELECT 1 AS dummy) AS x WHERE 1=0) AS ledger";
        }

        // Date filters
        $fromDate = request()->get('date_from') ? request()->get('date_from') . " 00:00:00" : "1970-01-01 00:00:00";
        $toDate   = request()->get('date_to')   ? request()->get('date_to') . " 23:59:59" : now()->endOfDay()->format("Y-m-d H:i:s");

        // If ALL users -> NO running balance, NO opening balance
        if ($mason === "ALL") { // Not in Use Right Now.
            $sql = $this->queryAllUsers($idList, $fromDate, $toDate, $limit, $offset);
        }else{
            // Single user -> FULL ledger with running balance
            $sql = $this->querySingleUser($idList, $fromDate, $toDate, $limit, $offset);
        }

      

        


        $myfile = fopen(public_path("/excel_exports/ledger_points/").$filename, "w");
        fputcsv($myfile,$headings);


         foreach(DB::cursor($sql) as $val)
        {
            if(empty($val->mason_details)){
                dd($val->mason_details);
            }
            $mason_details = json_decode($val->mason_details);
            // if(empty($val?->district)){
            //     dd($val);
            // }
            $address  = $val?->address1 . ", ".  $val?->address2 . ", ".  $val?->city . ", ".  $val?->district . ", ". $val?->state . ", ". $val?->pincode;
            $address  = strlen($address) == 10 ? '' : $address ;
            
       
            $content = [
                !empty($val->lifting_date) ? Carbon::parse($val->lifting_date)->toDateString() : "N/A",
                Carbon::parse($val->created_at)->toDateString(),
                $val->order_id ?? "",
                $mason_details == null ? "" : $mason_details->name,
                $mason_details == null ? "" : $mason_details->phone,
                $mason_details == null ? "" : ($mason_details->branch == null ? "" : $mason_details->branch->name),
                $mason_details == null ? "" : ($mason_details->te == null ? "" : $mason_details->te->code),
                $mason_details == null ? "" : ($mason_details->te == null ? "" : $mason_details->te->name),

                // Address fields (ONLY from catalogue redemption)
                $address,

                $val->description,
                $val->credit_point,

                //  Points
                $val->catalogue_point ?? 0,
                $val->tds_point ?? 0,
                $val->debit_point,
                $val->remaining_point,
            ];
            fputcsv($myfile,$content);
        }

        fclose($myfile);
        $filePath = public_path("/excel_exports/ledger_points/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);

    }

    // Export All
     public function exportAllLedger(Request $request)
    {
        set_time_limit(0);

        $filename = "All_Ledger_" . $this->getUniqueId() . ".csv";

        $headings = [
            "Lifting Date",
            "Creation Date",
            "Order No",
            "Name",
            "Phone No.",
            "Address",
            // "Address 2",
            // "City",
            // "District",
            // "State",
            // "Pincode",
            "Branch",
            "BDE Code",
            "BDE Name",
            "Description",
            "Credit Point",
            "Catalogue Point",
            "TDS Point",
            "Debit Point",
            "Remaining Balance",
        ];

        $sql = "
            WITH full_ledger AS (

                /* ---------- 1. Catalogue Redeem ---------- */
                SELECT 
                    ucr.id,
                    u.name AS mason_name,
                    u.phone,

                    ucr.address1,
                    ucr.address2,
                    ucr.city,
                    ucr.district,
                    ucr.state,
                    ucr.pincode,

                    b.name AS branch_name,
                    p.name AS parent_name,
                    p.emp_code AS parent_code,

                    ucr.order_id,
                    ucr.user_id,

                    0 AS credit_point,
                    ucr.catalogue_point AS catalogue_point,
                    ucr.catalogue_tds_point AS tds_point,
                    ucr.redeemed_point AS debit_point,

                    ucr.description,
                    ucr.created_at,
                    ucr.created_at AS ledger_date,
                    NULL AS lifting_date
                FROM user_catalogue_redeemtions ucr
                JOIN users u ON u.id = ucr.user_id
                LEFT JOIN branch b ON b.id = u.branch_id
                LEFT JOIN users p ON p.id = u.parent

                UNION ALL

                /* ---------- 2. Rewards ---------- */
                SELECT 
                    r.id,
                    u.name,
                    u.phone,

                    NULL, NULL, NULL, NULL, NULL, NULL,

                    b.name,
                    p.name,
                    p.emp_code,

                    NULL AS order_id,
                    r.user_id,

                    CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END,
                    0,
                    0,
                    CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END,

                    r.description,
                    r.created_at,
                    r.updated_at,
                    l.lifting_date
                FROM rewards r
                JOIN users u ON u.id = r.user_id
                LEFT JOIN branch b ON b.id = u.branch_id
                LEFT JOIN users p ON p.id = u.parent
                LEFT JOIN lifting l ON r.lifting_id = l.id
                WHERE r.is_eligible_for_ledger = 1

                UNION ALL

                /* ---------- 3. Reward History ---------- */
                SELECT 
                    rh.id,
                    u.name,
                    u.phone,

                    NULL, NULL, NULL, NULL, NULL, NULL,

                    b.name,
                    p.name,
                    p.emp_code,

                    NULL,
                    rh.user_id,

                    CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END,
                    0,
                    0,
                    CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END,

                    rh.description,
                    rh.created_at,
                    rh.reward_date_time,
                    l.lifting_date
                FROM reward_history rh
                JOIN users u ON u.id = rh.user_id
                LEFT JOIN branch b ON b.id = u.branch_id
                LEFT JOIN users p ON p.id = u.parent
                LEFT JOIN lifting l ON rh.lifting_id = l.id
                WHERE rh.is_eligible_for_ledger = 1

                UNION ALL

                /* ---------- 4. Rejected Redeem ---------- */
                SELECT 
                    rr.id,
                    u.name,
                    u.phone,

                    NULL, NULL, NULL, NULL, NULL, NULL,

                    b.name,
                    p.name,
                    p.emp_code,

                    NULL,
                    rr.user_id,

                    rr.point_credited,
                    0,
                    0,
                    0,

                    rr.description,
                    rr.created_at,
                    rr.created_at,
                    NULL
                FROM rejected_redeemtions rr
                JOIN users u ON u.id = rr.user_id
                LEFT JOIN branch b ON b.id = u.branch_id
                LEFT JOIN users p ON p.id = u.parent
            )

            SELECT *,
                SUM(credit_point - debit_point)
                OVER (PARTITION BY user_id ORDER BY ledger_date, id) AS remaining_point
            FROM full_ledger
            ORDER BY user_id, ledger_date, id
        ";

        $path = public_path("/excel_exports/ledger_points/");
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file = fopen($path . $filename, "w");
        fputcsv($file, $headings);

        foreach (DB::cursor($sql) as $val) {

            $address  = $val->address1 . ", ".  $val->address2 . ", ".  $val->city . ", ".  $val->district . ", ". $val->state . ", ". $val->pincode;
            $address  = strlen($address) == 10 ? '' : $address ;

            fputcsv($file, [
                $val->lifting_date ? Carbon::parse($val->lifting_date)->toDateString() : "N/A",
                Carbon::parse($val->created_at)->toDateString(),
                $val->order_id,
                $val->mason_name,
                $val->phone,
                $address,
                $val->branch_name,
                $val->parent_code,
                $val->parent_name,
                $val->description,
                $val->credit_point,
                $val->catalogue_point,
                $val->tds_point,
                $val->debit_point,
                $val->remaining_point,
            ]);
        }

        fclose($file);

        return response()
            ->download($path . $filename)
            ->deleteFileAfterSend(true);
    }

    // public function exportAllLedger_OLD(Request $request) {
       
    //     set_time_limit(0);
       
    //     $filename = "All_Ledger_".$this->getUniqueId().".csv";
    //     $headings = [
    //         "Lifting Date",
    //         "Creation Date",
    //         "Order No",
    //         "Name",
    //         "Phone No.",
    //         "Branch",
    //         "BDE Code",
    //         "BDE Name",
    //         "Description",
    //         "Credit Point",
    //         "Debit Point",
    //         "Remaining Balance",
    //     ];

    //     $sql = "
    //         WITH full_ledger AS (

    //             -- 1. Catalogue Redeem
    //             SELECT 
    //                 ucr.id,
    //                 u.name        AS mason_name,
    //                 u.phone       AS phone,
    //                 b.name        AS branch_name,
    //                 p.name        AS parent_name,
    //                 p.emp_code        AS parent_code,
    //                 ucr.order_id,
    //                 ucr.user_id,
    //                 0 AS credit_point,
    //                 ucr.redeemed_point AS debit_point,
    //                 ucr.description,
    //                 ucr.created_at,
    //                 ucr.created_at AS ledger_date,
    //                 NULL AS lifting_date
    //             FROM user_catalogue_redeemtions ucr
    //             JOIN users u          ON u.id = ucr.user_id
    //             LEFT JOIN branch b  ON b.id = u.branch_id
    //             LEFT JOIN users p     ON p.id = u.parent

    //             UNION ALL

    //             -- 2. Rewards
    //             SELECT 
    //                 r.id,
    //                 u.name        AS mason_name,
    //                 u.phone       AS phone,
    //                 b.name        AS branch_name,
    //                 p.name        AS parent_name,
    //                 p.emp_code        AS parent_code,
    //                 NULL AS order_id,
    //                 r.user_id,
    //                 CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END AS credit_point,
    //                 CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END AS debit_point,
    //                 r.description,
    //                 r.created_at,
    //                 r.updated_at AS ledger_date,
    //                 l.lifting_date
    //             FROM rewards r
    //             JOIN users u          ON u.id = r.user_id
    //             LEFT JOIN branch b  ON b.id = u.branch_id
    //             LEFT JOIN users p     ON p.id = u.parent
    //             LEFT JOIN lifting l   ON r.lifting_id = l.id
    //             WHERE r.is_eligible_for_ledger = 1

    //             UNION ALL

    //             -- 3. Reward History
    //             SELECT 
    //                 rh.id,
    //                 u.name        AS mason_name,
    //                 u.phone       AS phone,               
    //                 b.name        AS branch_name,
    //                 p.name        AS parent_name,
    //                 p.emp_code    AS parent_code,
    //                 NULL AS order_id,
    //                 rh.user_id,
    //                 CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END AS credit_point,
    //                 CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END AS debit_point,
    //                 rh.description,
    //                 rh.created_at,
    //                 rh.reward_date_time AS ledger_date,
    //                 l.lifting_date
    //             FROM reward_history rh
    //             JOIN users u          ON u.id = rh.user_id
    //             LEFT JOIN branch b  ON b.id = u.branch_id
    //             LEFT JOIN users p     ON p.id = u.parent
    //             LEFT JOIN lifting l   ON rh.lifting_id = l.id
    //             WHERE rh.is_eligible_for_ledger = 1

    //             UNION ALL

    //             -- 4. Rejected Redeem
    //             SELECT 
    //                 rr.id,
    //                 u.name        AS mason_name,
    //                 u.phone       AS phone,
    //                 b.name        AS branch_name,
    //                 p.name        AS parent_name,
    //                 p.emp_code    AS parent_code,
    //                 NULL AS order_id,
    //                 rr.user_id,
    //                 rr.point_credited AS credit_point,
    //                 0 AS debit_point,
    //                 rr.description,
    //                 rr.created_at,
    //                 rr.created_at AS ledger_date,
    //                 NULL AS lifting_date
    //             FROM rejected_redeemtions rr
    //             JOIN users u          ON u.id = rr.user_id
    //             LEFT JOIN branch b  ON b.id = u.branch_id
    //             LEFT JOIN users p     ON p.id = u.parent
    //         )

    //         SELECT
    //             *,
    //             SUM(credit_point - debit_point)
    //                 OVER (
    //                     PARTITION BY user_id
    //                     ORDER BY ledger_date, id
    //                 ) AS remaining_point
    //         FROM full_ledger
    //         ORDER BY user_id, ledger_date, id;
    //     " ;

    //     $myfile = fopen(public_path("/excel_exports/ledger_points/").$filename, "w");
    //     fputcsv($myfile,$headings);


    //      foreach(DB::cursor($sql) as $val)
    //     {
           
    //         // $mason_details = json_decode($val->mason_details);
    //         $content = [
    //             !empty($val->lifting_date) ? Carbon::parse($val->lifting_date)->toDateString() : "N/A",
    //             Carbon::parse($val->created_at)->toDateString(),
    //             $val->order_id,
    //             $val->mason_name,
    //             $val->phone,
    //             $val->branch_name,
    //             $val->parent_code,
    //             $val->parent_name,
    //             $val->description,
    //             $val->credit_point,
    //             $val->debit_point,
    //             $val->remaining_point,
    //         ];
    //         fputcsv($myfile,$content);
    //     }

    //     fclose($myfile);
    //     $filePath = public_path("/excel_exports/ledger_points/".$filename);
    //     return response()->download($filePath)->deleteFileAfterSend(true);

        
    // }

    // --------------------------
    // QUERY FOR ALL USERS
    // --------------------------
    private function queryAllUsers($idList, $fromDate, $toDate)
    {
        $sql = "
        SELECT 
            id,
            get_mason_details(user_id) AS mason_details,
            order_id,
            user_id,
            credit_point,
            debit_point,
            description,
            created_at,
            ledger_date,
            lifting_date,
            NULL AS remaining_point
        FROM (

            SELECT 
                ucr.id,
                ucr.order_id,
                ucr.user_id,
                0 AS credit_point,
                ucr.redeemed_point AS debit_point,
                ucr.description,
                ucr.created_at,
                ucr.created_at AS ledger_date,
                NULL AS lifting_date
            FROM user_catalogue_redeemtions ucr
            WHERE ucr.user_id IN ($idList)
            AND ucr.created_at BETWEEN '$fromDate' AND '$toDate'

            UNION ALL

            SELECT 
                r.id,
                NULL AS order_id,
                r.user_id,
                CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END AS credit_point,
                CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END AS debit_point,
                r.description,
                r.created_at,
                r.updated_at AS ledger_date,
                l.lifting_date
            FROM rewards r
            LEFT JOIN lifting l ON r.lifting_id = l.id
            WHERE r.user_id IN ($idList)
            AND r.is_eligible_for_ledger = 1
            AND r.updated_at BETWEEN '$fromDate' AND '$toDate'

            UNION ALL

            SELECT 
                rh.id,
                NULL AS order_id,
                rh.user_id,
                CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END AS credit_point,
                CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END AS debit_point,
                rh.description,
                rh.created_at,
                rh.reward_date_time AS ledger_date,
                l.lifting_date
            FROM reward_history rh
            LEFT JOIN lifting l ON rh.lifting_id = l.id
            WHERE rh.user_id IN ($idList)
            AND rh.is_eligible_for_ledger = 1
            AND rh.reward_date_time BETWEEN '$fromDate' AND '$toDate'

            UNION ALL

            SELECT 
                rr.id,
                NULL AS order_id,
                rr.user_id,
                rr.point_credited AS credit_point,
                0 AS debit_point,
                rr.description,
                rr.created_at,
                rr.created_at AS ledger_date,
                NULL AS lifting_date
            FROM rejected_redeemtions rr
            WHERE rr.user_id IN ($idList)
            AND rr.created_at BETWEEN '$fromDate' AND '$toDate'

        ) AS x
        ORDER BY user_id, ledger_date ASC
        ";

         return ($sql);
    }


    // --------------------------
    // QUERY FOR SINGLE USER
    // --------------------------

    private function querySingleUser($idList, $fromDate, $toDate, $limit, $offset)
    {
        $sql = "
        WITH full_ledger AS (

            /* ---------- 1. Catalogue Redeem ---------- */
            SELECT 
                ucr.id,
                get_mason_details(ucr.user_id) AS mason_details,
                IFNULL(ucr.order_id, '') AS order_id,
                ucr.user_id,

                0 AS credit_point,
                ucr.redeemed_point AS debit_point,

                ucr.catalogue_point,
                ucr.catalogue_tds_point AS tds_point,

                IFNULL(ucr.description, '') AS description,

                ucr.address1,
                ucr.address2,
                ucr.city,
                ucr.district,
                ucr.state,
                ucr.pincode,

                ucr.created_at,
                ucr.created_at AS ledger_date,
                NULL AS lifting_date

            FROM user_catalogue_redeemtions ucr
            WHERE ucr.user_id IN ($idList)

            UNION ALL

            /* ---------- 2. Rewards ---------- */
            SELECT 
                r.id,
                get_mason_details(r.user_id) AS mason_details,
                NULL AS order_id,
                r.user_id,

                CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END AS credit_point,
                CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END AS debit_point,

                0 AS catalogue_point,
                0 AS tds_point,

                r.description,

                NULL AS address1,
                NULL AS address2,
                NULL AS city,
                NULL AS district,
                NULL AS state,
                NULL AS pincode,

                r.created_at,
                r.updated_at AS ledger_date,
                l.lifting_date

            FROM rewards r
            LEFT JOIN lifting l ON r.lifting_id = l.id
            WHERE r.user_id IN ($idList)
            AND r.is_eligible_for_ledger = 1

            UNION ALL

            /* ---------- 3. Reward History ---------- */
            SELECT 
                rh.id,
                get_mason_details(rh.user_id) AS mason_details,
                NULL AS order_id,
                rh.user_id,

                CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END AS credit_point,
                CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END AS debit_point,

                0 AS catalogue_point,
                0 AS tds_point,

                rh.description,

                NULL AS address1,
                NULL AS address2,
                NULL AS city,
                NULL AS district,
                NULL AS state,
                NULL AS pincode,

                rh.created_at,
                rh.reward_date_time AS ledger_date,
                l.lifting_date

            FROM reward_history rh
            LEFT JOIN lifting l ON rh.lifting_id = l.id
            WHERE rh.user_id IN ($idList)
            AND rh.is_eligible_for_ledger = 1

            UNION ALL

            /* ---------- 4. Rejected Redeem ---------- */
            SELECT 
                rr.id,
                get_mason_details(rr.user_id) AS mason_details,
                NULL AS order_id,
                rr.user_id,

                rr.point_credited AS credit_point,
                0 AS debit_point,

                0 AS catalogue_point,
                0 AS tds_point,

                rr.description,

                NULL AS address1,
                NULL AS address2,
                NULL AS city,
                NULL AS district,
                NULL AS state,
                NULL AS pincode,

                rr.created_at,
                rr.created_at AS ledger_date,
                NULL AS lifting_date

            FROM rejected_redeemtions rr
            WHERE rr.user_id IN ($idList)
        ),

        /* ---------- OPENING BALANCE ---------- */
        opening_balance AS (
            SELECT 
                user_id,
                COALESCE(SUM(credit_point - debit_point), 0) AS opening_balance
            FROM full_ledger
            WHERE ledger_date < '$fromDate'
            GROUP BY user_id
        ),

        /* ---------- FILTERED LEDGER ---------- */
        filtered_ledger AS (
            SELECT 
                fl.*,
                (credit_point - debit_point) AS txn_balance
            FROM full_ledger fl
            WHERE fl.ledger_date BETWEEN '$fromDate' AND '$toDate'
        ),

        /* ---------- RUNNING BALANCE ---------- */
        running AS (
            SELECT 
                fl.id,
                fl.mason_details,
                fl.order_id,
                fl.user_id,

                fl.credit_point,
                fl.debit_point,
                fl.catalogue_point,
                fl.tds_point,

                fl.description,

                fl.address1,
                fl.address2,
                fl.city,
                fl.district,
                fl.state,
                fl.pincode,

                fl.created_at,
                fl.ledger_date,
                fl.lifting_date,

                COALESCE(ob.opening_balance, 0) AS opening_balance,
                (
                    COALESCE(ob.opening_balance, 0)
                    + SUM(fl.txn_balance) OVER (
                        PARTITION BY fl.user_id
                        ORDER BY fl.ledger_date, fl.id
                    )
                ) AS remaining_point

            FROM filtered_ledger fl
            LEFT JOIN opening_balance ob ON fl.user_id = ob.user_id
        ),

        /* ---------- OPENING ROW ---------- */
        opening_rows AS (
            SELECT
                NULL AS id,
                get_mason_details(ob.user_id) AS mason_details,
                NULL AS order_id,
                ob.user_id,

                0 AS credit_point,
                0 AS debit_point,
                0 AS catalogue_point,
                0 AS tds_point,

                CONCAT('Opening Balance as of ', '$fromDate') AS description,

                NULL AS address1,
                NULL AS address2,
                NULL AS city,
                NULL AS district,
                NULL AS state,
                NULL AS pincode,

                DATE_SUB('$fromDate', INTERVAL 1 SECOND) AS created_at,
                DATE_SUB('$fromDate', INTERVAL 1 SECOND) AS ledger_date,
                NULL AS lifting_date,

                ob.opening_balance AS opening_balance,
                ob.opening_balance AS remaining_point
            FROM opening_balance ob
        )

        SELECT * FROM opening_rows
        UNION ALL
        SELECT * FROM running
        ORDER BY user_id, ledger_date ASC
        ";

        return $sql;
    }

    // private function querySingleUser_OLD($idList, $fromDate, $toDate, $limit, $offset)
    // {
    //     $sql = "
    //         WITH full_ledger AS (

    //             -- 1. Catalogue Redeem
    //             SELECT 
    //                 ucr.id,
    //                 get_mason_details(ucr.user_id) AS mason_details,
    //                 IFNULL(ucr.order_id, '') AS order_id,
    //                 ucr.user_id,
    //                 0 AS credit_point,
    //                 ucr.redeemed_point AS debit_point,
    //                 IFNULL(ucr.description, '') AS description,
    //                 ucr.created_at,
    //                 ucr.created_at AS ledger_date,
    //                 NULL AS lifting_date
    //             FROM user_catalogue_redeemtions ucr
    //             WHERE ucr.user_id IN ($idList)

    //             UNION ALL

    //             -- 2. Rewards
    //             SELECT 
    //                 r.id,
    //                 get_mason_details(r.user_id) AS mason_details,
    //                 NULL AS order_id,
    //                 r.user_id,
    //                 CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END AS credit_point,
    //                 CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END AS debit_point,
    //                 r.description,
    //                 r.created_at,
    //                 r.updated_at AS ledger_date,
    //                 l.lifting_date
    //             FROM rewards r
    //             LEFT JOIN lifting l ON r.lifting_id = l.id
    //             WHERE r.user_id IN ($idList)
    //             AND r.is_eligible_for_ledger = 1

    //             UNION ALL

    //             -- 3. Reward History
    //             SELECT 
    //                 rh.id,
    //                 get_mason_details(rh.user_id) AS mason_details,
    //                 NULL AS order_id,
    //                 rh.user_id,
    //                 CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END AS credit_point,
    //                 CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END AS debit_point,
    //                 rh.description,
    //                 rh.created_at,
    //                 rh.reward_date_time AS ledger_date,
    //                 l.lifting_date
    //             FROM reward_history rh
    //             LEFT JOIN lifting l ON rh.lifting_id = l.id
    //             WHERE rh.user_id IN ($idList)
    //             AND rh.is_eligible_for_ledger = 1

    //             UNION ALL

    //             -- 4. Rejected Redeem
    //             SELECT 
    //                 rr.id,
    //                 get_mason_details(rr.user_id) AS mason_details,
    //                 NULL AS order_id,
    //                 rr.user_id,
    //                 rr.point_credited AS credit_point,
    //                 0 AS debit_point,
    //                 rr.description,
    //                 rr.created_at,
    //                 rr.created_at AS ledger_date,
    //                 NULL AS lifting_date
    //             FROM rejected_redeemtions rr
    //             WHERE rr.user_id IN ($idList)
    //         ),

    //         opening_balance AS (
    //             SELECT 
    //                 user_id,
    //                 COALESCE(SUM(credit_point - debit_point), 0) AS opening_balance
    //             FROM full_ledger
    //             WHERE ledger_date < '$fromDate'
    //             GROUP BY user_id
    //         ),

    //         filtered_ledger AS (
    //             SELECT 
    //                 fl.*,
    //                 (credit_point - debit_point) AS txn_balance
    //             FROM full_ledger fl
    //             WHERE fl.ledger_date BETWEEN '$fromDate' AND '$toDate'
    //         ),

    //         running AS (
    //             SELECT 
    //                 fl.id,
    //                 fl.mason_details,
    //                 fl.order_id,
    //                 fl.user_id,
    //                 fl.credit_point,
    //                 fl.debit_point,
    //                 fl.description,
    //                 fl.created_at,
    //                 fl.ledger_date,
    //                 fl.lifting_date,
    //                 COALESCE(ob.opening_balance, 0) AS opening_balance,
    //                 (
    //                     COALESCE(ob.opening_balance, 0) 
    //                     + SUM(fl.txn_balance) OVER (PARTITION BY fl.user_id ORDER BY fl.ledger_date, fl.id)
    //                 ) AS remaining_point
    //             FROM filtered_ledger fl
    //             LEFT JOIN opening_balance ob ON fl.user_id = ob.user_id
    //         ),

    //         opening_rows AS (
    //             SELECT
    //                 NULL AS id,
    //                 get_mason_details(ob.user_id) AS mason_details,
    //                 NULL AS order_id,
    //                 ob.user_id AS user_id,
    //                 0 AS credit_point,
    //                 0 AS debit_point,
    //                 CONCAT('Opening Balance as of ', '$fromDate') AS description,
    //                 DATE_SUB('$fromDate', INTERVAL 1 SECOND) AS created_at,
    //                 DATE_SUB('$fromDate', INTERVAL 1 SECOND) AS ledger_date,
    //                 NULL AS lifting_date,
    //                 ob.opening_balance AS opening_balance,
    //                 ob.opening_balance AS remaining_point
    //             FROM opening_balance ob
    //         )

    //         SELECT * FROM opening_rows
    //         UNION ALL
    //         SELECT * FROM running
    //         ORDER BY user_id, ledger_date ASC
    //         ";

    //     return $sql;
    // }



    public function getLedger(Request $request)
    {
        // return "service unavailable by dev";
        // $a = UserCatalogueRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`")->where('user_id', 34646);
        // $b = Reward::selectRaw("get_mason_details(`user_id`) AS mason_details,NULL AS `order_id`,`user_id`, `point` AS `credit_point`,NULL AS `debit_point`,`description`,`created_at`")->where('is_verified', 1)->where('user_id', 34646)->union($a);
        // $c = RejectedRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,NULL AS `order_id`,`user_id`, `point_credited` AS `credit_point`,NULL AS `debit_point`,`description`,`created_at`")->where('user_id', 34646)->union($b)->orderBy('created_at')->get();
        // return $c;
        $loggedUser=Auth::user();
        $user     = $request->user ;
        if($user == "ALL"){
            if($loggedUser->role > 6)
            {
                $allocated_branches=json_decode($loggedUser->allocated_branches);
                $userIds=implode(",",User::whereIn('branch_id',$allocated_branches)->pluck('id')->toArray());
                //for specific branch users those who belongs to logged user branch
                // $data = DB::select("SELECT * FROM (
                //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at` 
                //     FROM `user_catalogue_redeemtions` WHERE `user_id` IN (".$userIds.")
                //     UNION ALL      
                //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
                //     FROM `rewards` WHERE `user_id` IN (".$userIds.") AND`is_verified`='1'
                //     UNION ALL      
                //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
                //     FROM `rejected_redeemtions` WHERE `user_id` IN (".$userIds.")
                //     )P 
                //     ORDER BY `created_at`");
                $data = DB::select("SELECT * FROM (
                        SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
                        created_at AS ledger_date FROM `user_catalogue_redeemtions` WHERE `user_id` IN (".$userIds.")
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`,CASE WHEN `is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `is_verified` != ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS debit_point,`description`,`created_at`,
                        updated_at AS ledger_date FROM `rewards` WHERE `user_id` IN (".$userIds.") AND `is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`,CASE WHEN `is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `is_verified` != ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS debit_point,`description`,`created_at`,
                        reward_date_time AS ledger_date FROM `reward_history` WHERE `user_id` IN (".$userIds.") AND `is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
                        created_at AS ledger_date FROM `rejected_redeemtions` WHERE `user_id` IN (".$userIds.")
                        )P
                        ORDER BY ledger_date");
            }
            else
            {
                //for all branch users
                // $data = DB::select("SELECT * FROM (
                //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at` 
                //     FROM `user_catalogue_redeemtions`
                //     UNION ALL      
                //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
                //     FROM `rewards` WHERE `is_verified`='1'
                //     UNION ALL      
                //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
                //     FROM `rejected_redeemtions`
                //     )P 
                //     ORDER BY `created_at`");
                $data = DB::select("SELECT * FROM (
                        SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
                        created_at AS ledger_date FROM `user_catalogue_redeemtions`
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`,CASE WHEN `is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `is_verified` != ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS debit_point,`description`,`created_at`,
                        updated_at AS ledger_date FROM `rewards` WHERE `is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`,CASE WHEN `is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `is_verified` != ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS debit_point,`description`,`created_at`,
                        reward_date_time AS ledger_date FROM `reward_history` WHERE `is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
                        created_at AS ledger_date FROM `rejected_redeemtions`
                        )P
                        ORDER BY ledger_date");
            }
                
        }else{
            // $data = DB::select("SELECT * FROM (
            //     SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
            //     FROM `user_catalogue_redeemtions` WHERE `user_id`= ?  
            //     UNION ALL      
            //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`,
            //     FROM `rewards` WHERE `user_id`=?
            //     AND `is_verified`='1'
            //     UNION ALL      
            //     SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
            //     FROM `rejected_redeemtions` WHERE `user_id`=?
            //     )P 
            //     ORDER BY `created_at`", [$user, $user, $user]);
            $data = DB::select("SELECT * FROM (
                        SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
                        created_at AS ledger_date FROM `user_catalogue_redeemtions` WHERE `user_id`= ?
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`,CASE WHEN `is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `is_verified` != ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS debit_point,`description`,`created_at`,
                        updated_at AS ledger_date FROM `rewards` WHERE `user_id` =? AND `is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`,CASE WHEN `is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `is_verified` != ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS debit_point,`description`,`created_at`,
                        reward_date_time AS ledger_date FROM `reward_history` WHERE `user_id` =? AND `is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
                        created_at AS ledger_date FROM `rejected_redeemtions` WHERE `user_id`=?
                        )P
                        ORDER BY ledger_date", [$user, $user, $user, $user]);
        }
       
       
      return response()->json(['success'=> true, 'data'=> $data], 200);
    }

    //-------Bulk Import------------

    public function showBulkUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.bulk-upload') ;
        if($request->session()->exists('mason_import')){
            return view('admin.masons.progress') ;
        }
       // return "Hi";
        return view('admin.masons.bulk-upload') ;
       //return redirect(route('employee.upload.show'));
    }

    
    public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.bulk-upload') ;
        set_time_limit(0);
        try {

            if($request->hasFile('csvFile')){
                $file       = $request->file('csvFile');
                $folderPath = Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  Storage::disk('public')->path($folderPath) ;
                $records = array_map('str_getcsv', file($fileWithPath));
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = 0; $unProcessedCount = 0;
                session()->put('mason_import', $fileWithPath) ;
                session()->put('mason_count', 0) ;
                $unprocessedData=[];
                $i=1;
                $importingFile = fopen($fileWithPath, 'r');
                // $headers = fgetcsv($importingFile);
                $row = [];
                $rowCount = 0;
                while (($rowLine = fgetcsv($importingFile)) !== false) {
                        if($rowCount > 0){
                            $row = array_map(function ($value) {
                                return str_replace(["\r", "\n"], ' ', $value);
                            }, $rowLine);
                            if(!empty($row[11])){

                                $teId = $this->getTeIdByCode($row[16] ?? null) ;

                                if($user = $this->isMasonExist($row[11],$row[8])){
                                    if($this->isPhoneExist($row[11],$user->id))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", duplicate phone number found. ");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    if($this->isAadhaarExist($row[8],$user->id))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", duplicate aadhaar number found. ");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    $dealerIds=$this->getDealerIds($row[17] ?? null);
                                    $existingDealerIds=MasonDealer::where('mason_id',$user->id)->pluck('dealer_id')->toArray();
                                    if($dealerIds != null)
                                    {
                                        $dealerIds = implode(',',$dealerIds);
                                    }
                                    else
                                    {
                                        $dealerIds = $user->dealer_ids;
                                    }
                                    $user->update([
                                        'name'=> $row[0] ?? $user->name,
                                        'address1' => $row[1] ?? $user->address1,
                                        'address2' => $row[2] ?? $user->address2,
                                        'city' => $row[3] ?? $user->city,
                                        'district' => $row[4] ?? $user->district,
                                        'state' => $row[5] ?? $user->state,
                                        'country' => $row[6] ?? $user->country,
                                        'pincode' => $row[7] ?? $user->pincode,
                                        'aadhaar_no' => $row[8] ?? $user->aadhaar_no,
                                        // 'aadhaar_doc' => $row[9] ?? $user->aadhaar_doc,
                                        'dob' => $row[10] != '' ? Carbon::parse($row[10])->format('Y-m-d') : $user->dob,
                                        'phone' => $row[11] ?? $user->phone,
                                        'marital_status' => $row[12] == 1 || $row[12] == 0 ? $row[12] : $user->marital_status,
                                        'spouse_name' => $row[12] == 1 ? $row[13] ?? $user->spouse_name : $user->spouse_name,
                                        'spouse_dob' => $row[12] == 1 ? Carbon::parse($row[14])->format('Y-m-d') ?? $user->spouse_dob : $user->spouse_dob,
                                        'branch_id' => $this->getBranchIdByCode($row[15] ?? $user->branch_id),
                                        'parent' => $teId,
                                        'dealer_ids'=> $dealerIds,
                                        'role' =>2,
                                                
                                    ]);
                                    //Deleting Dealer Ids
                                    if(count($diffDealerIds = array_diff($existingDealerIds, explode(",", $dealerIds))) > 0)
                                    {
                                        foreach($diffDealerIds as $diffDealerId)
                                        {
                                            MasonDealer::where([
                                                'mason_id'=>$user->id,
                                                'dealer_id'=>$diffDealerId,
                                            ])->delete();
                                        }
                                    }
                                    //Adding new Dealer Ids
                                    if(count($diffDealerIds = array_diff(explode(",", $dealerIds),$existingDealerIds)) > 0)
                                    {
                                        foreach($diffDealerIds as $diffDealerId)
                                        {
                                            MasonDealer::create([
                                                'mason_id'=>$user->id,
                                                'dealer_id'=>$diffDealerId,
                                            ]);
                                        }
                                    }
                                    $count++ ;
                                    
                                    // dd(array_diff($dealerIds, $existingDealerIds));
                                    // array_push($unprocessedData,"<br>In row ".$i." Mason already exist");
                                    // $unProcessedCount++ ;
                                }
                                else
                                {
                                    $dealerIds=$this->getDealerIds($row[17] ?? null);
                                    $user = User::create([
                                        'name'=> $row[0] ?? null,
                                        'address1' => $row[1] ?? null,
                                        'address2' => $row[2] ?? null,
                                        'city' => $row[3] ?? null,
                                        'district' => $row[4] ?? null,
                                        'state' => $row[5] ?? null,
                                        'country' => $row[6] ?? null,
                                        'pincode' => $row[7] ?? null,
                                        'aadhaar_no' => $row[8] ?? null,
                                        'aadhaar_doc' => $row[9] ?? null,
                                        'dob' => $row[10] != '' ? $row[10] : null,
                                        'phone' => $row[11] ?? null,
                                        'marital_status' => $row[12] == 1 || $row[12] == 0 ? $row[12] : null,
                                        'spouse_name' => $row[12] == 1 ? $row[13] ?? null : null,
                                        'spouse_dob' => $row[12] == 1 ? $row[14] ?? null : null,
                                        'branch_id' => $this->getBranchIdByCode($row[15] ?? null),
                                        'parent' => $teId,
                                        'created_by' => $teId,
                                        'dealer_ids'=> $dealerIds != null ? implode(',',$dealerIds) : null,
                                        'role' =>2,
                                        'registration_point' => $this->getRegPoint(),
                                                
                                    ]);

                                    // for add Registration Bonus points
                                    Reward::create([
                                        'user_id'  => $user->id, 
                                        'bag'         => 0, 
                                        'description'         => 'Registration bonus points', 
                                        'point'       =>  $this->getRegPoint(),
                                        'is_verified' => 1 ,
                                        'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_YES,
                                        'is_bonus' => 1
                                    ]) ;
                                    if($dealerIds != null)
                                    {
                                        foreach($dealerIds as $dealerId)
                                        {
                                            MasonDealer::create([
                                                'mason_id'=>$user->id,
                                                'dealer_id'=>$dealerId,
                                            ]);
                                        }
                                    }
                                    
                                    $count++ ;
                                    $this->updatePoint($user->id);
                                             
                                }
                                session()->put('mason_count', $count) ;
                                
                                // $request->session()->save();
                                // sleep(10) ;
    
                                if($count == $lineSllice){
                                    $request->session()->save();
                                    sleep(1) ;
                                }
                               
                              //  echo session()->get('total_count');
                               
                            
                            }
                           
                        
                        }
                        $i++;
                        $rowCount++;
                    }
                    fclose($importingFile);
                 $request->session()->forget('mason_import');
                
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('mason_count').' records processed. & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200); 
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('mason_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }
    
    public function getProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.bulk-upload') ;
        if($request->session()->has('mason_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('mason_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('mason_count').' records procxessed.'], 200); ;

    }

    public function isMasonExist($phone,$aadhaar_no)
    {
       $user = User::where('phone', $phone)->orWhere('aadhaar_no',$aadhaar_no)->first() ;
       return $user ;
    }

    public function isPhoneExist($phoneNumber,$userId)
    {
        $user=User::where('phone',$phoneNumber)->where('id','!=',$userId)->get();
        if(count($user) > 0)
        {
            return true;
        }
        return false;
    }

    public function isAadhaarExist($aadhaarNumber,$userId)
    {
        $user=User::where('aadhaar_no',$aadhaarNumber)->where('id','!=',$userId)->get();
        if(count($user) > 0)
        {
            return true;
        }
        return false;
    }

    public function getTeIdByCode($empCode)
    {
        $user = User::where('emp_code', $empCode)->first() ;
        if($user == null)
        {
            return null;
        }
       return $user->id ;
    }
    public function getDealerIds($dealerCodes)
    {
        if($dealerCodes == null)
        {
            return null;
        }
        $dealerCodes=explode(',',$dealerCodes);
        $dealerIds=[];
        foreach($dealerCodes as $dealerCode)
        {
            $user = User::where('emp_code', $dealerCode)->first();
            if($user != null)
            {
                array_push($dealerIds,$user->id);
            }
        }
        return $dealerIds;
    }

    //Bulk Aadhaar Doc Upload
    public function showAadhaarDocUploadForm()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.bulk-upload') ;
        //return "working";
        return view('admin.masons.images-upload') ;
    }
    public function uploadAadhaarDoc(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.bulk-upload') ;
        try {
            
            set_time_limit(0);
            $count = 0 ;

            // Starting File Upload.
            foreach ($request->my_files as $key => $file) {
                if(is_file($file)) {   

                    $fpath = $this->uploadBulkAadhaarDoc($file) ;
                    $count++ ;
                }
            }
            return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Upload Successfull '.$count.' records uploaded.'], 200); ;


        } catch (\Exception $ex) {
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$ex->getMessage()], 200); ;
        }
    }

    // public function upDateMasonRegP()
    // {
    //     // $temp=[];
    //     // for($i=30803;$i<=33336;$i++)
    //     // {
    //     //     $this->updatePoint($i);
            
    //     // }
    //     $users=User::where('role',2)->get();
    //     $userIds=[];
    //     foreach($users as $user)
    //     {
    //         $userIds[]=$user->id;
    //     }
    //     $rewards=Reward::all();
    //     $rewardIds=[];
    //     foreach($rewards as $reward)
    //     {
    //         $rewardIds[]=$reward->user_id;
    //     }
    //     return array_diff($rewardIds,$userIds);
    //     return "done";
    // }

    public function export(Request $request) 
    {
        $baseUrl= $_SERVER['SERVER_NAME'];
        $baseUrl= "https://".$baseUrl."/public/";
        $basePath = dirname(base_path())."/public/";
        $numberOfRecords = User::where('role', 2)->count();
        set_time_limit(0);
        $filename = "Masons_".$this->getUniqueId().".csv";
        $headings = [
            'Name',
            'Image',
            'Address1',
            'Address2',
            'City',
            'District',
            'State',              
            'Country',              
            'Pincode',              
            'Aadhaar_no', 
            'Aadhaar_Doc',
            'Voter Number', 
            'Voter Doc',             
            'Dob',              
            'Phone',     
            'Marital Status',     
            'Spouse Name',     
            'Spouse Dob',     
            'Branch', 
            'Zone', 
            'Status',    
            'Disable Reason',    
            'Disable Date',    
            'Created By',
            'Linked BDE',
            'Linked Dealer Code',
            'Linked Dealer Name',
            'Points',     
            'Last Login Date Time',     
            'Login Status',     
            'Device Type',     
            'Device Name', 
            'App Version', 
            'Created At',
        ];
            
        $myfile = fopen(public_path("/excel_exports/masons/").$filename, "w");
        fputcsv($myfile,$headings);
        $fetchDataLimit = 1000;
        $fetchDataFrom = 0;
        $i = 0;
        $query = User::with('mason_dealers')->with('te_linked')->with('states')->with('branch')->with('by_created')->where('role', 2);
        if($request->has('fromDate', 'toDate', 'filter_by') && !empty('filter_by') && !empty($request->fromDate) && !empty($request->toDate) &&  $request->fromDate <= $request->toDate)
        {
            if($request->filter_by == 2)
            {
                $query = $query->whereDate('disable_date_time', '>=', $request->fromDate)->whereDate('disable_date_time', '<=', $request->toDate);
            }
            else
            {
                $query = $query->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
            }
        }
        if($request->has('status') && $request->status != null && $request->filter_by != 2)
        {
            $query = $query->where('status', $request->status);
        }
        while($i < $numberOfRecords)
        {
            $data = $query->orderBy('id', 'DESC')->skip($fetchDataFrom)->take($fetchDataLimit)->get();
            foreach($data as $val)
            {
                $dealers="";
                $dealerCodes="";
                $i = 0;
                foreach($val->mason_dealers as $mason_dealer)
                {
                    if($i!=0)
                    {
                        $dealers.=", ";
                        $dealerCodes.=", ";
                    }
                    $dealers.=$mason_dealer->dealer->name ?? "";
                    $dealerCodes.=$mason_dealer->dealer->emp_code ?? "";
                    $i++;
                }
                $content = [
                    $val->name,
                    $val->profile_pic == null ? "" : $val->profile_pic,
                    $val->address1,
                    $val->address2,
                    $val->city,
                    $val->district,
                    $val->state,
                    $val->country,
                    $val->pincode,
                    "'".$val->aadhaar_no,
                    $val->aadhaar_doc == null ? "" : $baseUrl."aadhaar/".$val->aadhaar_doc,
                    ($val->branch->state->is_voter_require ?? 0) == State::VOTER_REQUIRE_YES ? (empty($val->voter_number) ? "No Voter Number Found." : ("'".$val->voter_number)) : "N/A",
                    (!empty($val->voter_doc) && file_exists($basePath.$val->voter_doc)) ? ($baseUrl.$val->voter_doc) : (($val->branch->state->is_voter_require ?? 0) == State::VOTER_REQUIRE_YES ? "No Voter Found." : "N/A"),
                    $val->dob,
                    $val->phone,
                    $val->marital_status == '1' ? "Yes" : "",
                    $val->spouse_name,
                    $val->spouse_dob,
                    $val->branch->name ?? "",
                    $val->branch->zone->name ?? "",
                    $val->status == 1 ? "Active" : "Disabled",
                    $val->status == 1 ? "" : $val->disable_reason,
                    $val->disable_date_time != null ? Carbon::parse($val->disable_date_time)->toDateString() : "",
                    $val->by_created->name ?? "",
                    $val->te_linked->name ?? "",
                    $dealerCodes,
                    $dealers,
                    $val->points,
                    $val->last_login_date_time, 
                    $val->login_status == 1 ? "Y" : "N", 
                    $val->login_device_type,
                    $val->login_device_name,
                    $val->app_version,
                    $val->created_at,
                ];
                fputcsv($myfile,$content);
            }
            $fetchDataFrom += $fetchDataLimit;
            $i += $fetchDataLimit;
        }
        fclose($myfile);
        $filePath = public_path("/excel_exports/masons/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
        // return Excel::download(new MasonExport, 'users.xlsx');
    }

    public function masonTransferInterface() 
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.transfer') ;
        $teList = User::whereIn('role', ['1'])->get();
        return view('admin.masons.transfer-masons')->with([
            "teList" => $teList
        ]);
    }
    public function masonTransfer(Request $request) 
    {
        try 
        {
            \Helper::checkIsUserAuthorizeToPerformTheTask('masons.transfer') ;
            $request->validate(
                [
                    "transfer_from_te_id" => "required|integer",
                    "transfer_to_te_id" => "required|integer",
                    "masons" => "required|array|min:1",
                    "masons.*" => "required|integer",
                ],
                [],
                [
                    "transfer_from_te_id" => "Transfer from BDE",
                    "transfer_to_te_id" => "Transfer to BDE",
                ]
            );
            if($request->transfer_from_te_id == $request->transfer_to_te_id)
            {
                Flash::error('Transferring BDE cannot not be same');
                return redirect()->back()->withInput();
            }
            $transferFrom = User::whereIn('role', ['1'])->where("id", $request->transfer_from_te_id)->first();
            if(empty($transferFrom))
            {
                Flash::error('Invalid BDE of Transfer from.');
                return redirect()->back()->withInput();
            }
            $transferTo = User::whereIn('role', ['1'])->where("id", $request->transfer_to_te_id)->first();
            if(empty($transferTo))
            {
                Flash::error('Invalid BDE of Transfer to.');
                return redirect()->back()->withInput();
            }
            $validMasonIDs = User::whereIn("id", $request->masons)->where("parent", $request->transfer_from_te_id)->pluck("id")->toArray();
            $validInvalidMasonDiff = array_diff($request->masons, $validMasonIDs);
            if(count($validInvalidMasonDiff) > 0)
            {
                $invalidMason = User::find($validInvalidMasonDiff[0]);
                if(empty($invalidMason))
                {
                    Flash::error('Invalid Contractor found.');
                }
                else
                {
                    Flash::error('Invalid Contractor '.$invalidMason->name.' - '.$invalidMason->phone.'.');
                }
                return redirect()->back()->withInput();
            }
            \DB::beginTransaction();
            User::whereIn("id", $request->masons)->update([
                "parent" => $request->transfer_to_te_id
            ]);
            \DB::commit();
            Flash::Success('Contractors has been transferred successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            \DB::rollback();
            Flash::error('Error : '.$e->getMessage());
        }
    }

       public function showBulkUploadsForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('masons.bulk-upload') ;
        if($request->session()->exists('mason_import')){
            return view('admin.masons.progress') ;
        }
       // return "Hi";
        return view('admin.masons.bulk-upload-point') ;
       //return redirect(route('employee.upload.show'));
    }


//   public function bulkPointManipulation(Request $request)
// {
//     try {
//        // \Helper::checkIsUserAuthorizeToPerformTheTask('masons.bulk-upload');
  
//         $request->validate([
//             'csvFile' => 'required|file|mimes:csv,txt|max:10240'
//         ]);

//         $file = $request->file('csvFile');
//         $csv = array_map('str_getcsv', file($file->getRealPath()));
        
      
//         $headers = array_map(function ($header) {
//             return trim(preg_replace('/^\xEF\xBB\xBF/', '', $header));
//         }, $csv[0]);
//         unset($csv[0]);
        
//         $requiredColumns = ['phone', 'points_earned', 'points_reduced', 'description', 'remarks'];
//         $missingColumns = array_diff($requiredColumns, $headers);
        
//         if (!empty($missingColumns)) {
//             return response()->json([
//                 'success' => false,
//                 'import_status' => false,
//                 'message' => 'Missing required columns: ' . implode(', ', $missingColumns)
//             ]);
//         }
        
//         $successCount = 0;
//         $errorCount = 0;
//         $errors = [];
//         $totalRows = count($csv);
        
        
        
//         foreach ($csv as $rowIndex => $row) {
            
//             \DB::beginTransaction();
            
//             try {
              
//                 if (empty(array_filter($row))) {
//                     \DB::rollback();
//                     continue;
//                 }
                
               
//                 $row = array_slice($row, 0, count($headers));
//                 $data = array_combine($headers, array_map('trim', $row));

//                 if ($data === false) {
//                     $errors[] = "Row " . ($rowIndex + 2) . ": Invalid data format";
//                     $errorCount++;
//                     \DB::rollback();
//                     continue;
//                 }
                
//                 $phone = $data['phone'] ?? '';
//                 $pointsEarned = (float) ($data['points_earned'] ?? 0);
//                 $pointsReduced = (float) ($data['points_reduced'] ?? 0);
//                 $description = $data['description'] ?? '';
//                 $remarks = $data['remarks'] ?? '';
                
              
//                 if (empty($phone)) {
//                     $errors[] = "Row " . ($rowIndex + 2) . ": Phone number is required";
//                     $errorCount++;
//                     \DB::rollback();
//                     continue;
//                 }
                
               
//                 if ($pointsEarned > 0 && $pointsReduced > 0) {
//                     $errors[] = "Row " . ($rowIndex + 2) . ": Both points_earned ({$pointsEarned}) and points_reduced ({$pointsReduced}) cannot be greater than 0 for phone {$phone}";
//                     $errorCount++;
//                     \DB::rollback();
//                     continue;
//                 }
                
              
//                 if ($pointsEarned <= 0 && $pointsReduced <= 0) {
//                     $errors[] = "Row " . ($rowIndex + 2) . ": Either points_earned or points_reduced must be greater than 0 for phone {$phone}";
//                     $errorCount++;
//                     \DB::rollback();
//                     continue;
//                 }
                
               
//                 $user = User::where('phone', $phone)
//                            ->where('role', 2)
//                            ->first();
                
//                 if (!$user) {
//                     $errors[] = "Row " . ($rowIndex + 2) . ": Mason not found with phone {$phone}";
//                     $errorCount++;
//                     \DB::rollback();
//                     continue;
//                 }
                
                
//                 if ($pointsEarned > 0) {
//                     $input = [
//                         'user_id' => $user->id,
//                         'point' => $pointsEarned,
//                         'is_verified' => 1,
//                         'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_YES,
//                         'description' => $description,
//                         'remarks' => $remarks,
//                     ];
                    
//                     $reward = Reward::create($input);
                    
               
//                     $logData = [
//                         'table_id' => $reward->id,
//                         'user_id' => \Auth::user()?->id,
//                         'model_name' => 'Reward',
//                         'request' => json_encode($input),
//                         'response' => json_encode($reward),
//                         'action' => 'create',
//                         'remarks' => 'Point Added - Bulk Upload',
//                     ];
                    
//                     $this->createLog($logData);
//                 }
                
                
//                 if ($pointsReduced > 0) {
                 
//                     if ($user->points < $pointsReduced && \Auth::user()->role != 5) {
//                         $errors[] = "Row " . ($rowIndex + 2) . ": Deduct point ({$pointsReduced}) can't be greater than net point ({$user->points}) for mason {$phone}";
//                         $errorCount++;
//                         \DB::rollback();
//                         continue;
//                     }
                    
//                     $input = [
//                         'user_id' => $user->id,
//                         'redeemed_point' => $pointsReduced,
//                         'description' => $description,
//                         'remarks' => $remarks,
//                     ];
                    
//                     $redemption = UserCatalogueRedeemtion::create($input);
                    
                 
//                     $logData = [
//                         'table_id' => $redemption->id,
//                         'user_id' => \Auth::user()?->id,
//                         'model_name' => 'UserCatalogueRedeemtion',
//                         'request' => json_encode($input),
//                         'response' => json_encode($redemption),
//                         'action' => 'create',
//                         'remarks' => 'Point Subtracted - Bulk Upload',
//                     ];
                    
//                     $this->createLog($logData);
//                 }
                
           
//                 $this->updatePoint($user->id);
                
               
//                 \DB::commit();
//                 $successCount++;
                
//             } catch (\Exception $e) {
                
//                 \DB::rollback();
//                 $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
//                 $errorCount++;
//             }
//         }
        
    
//         $request->session()->put('point_count', $successCount);
        
     
//         if ($errorCount > 0 && $successCount == 0) {
          
//             return response()->json([
//                 'success' => false,
//                 'import_status' => false,
//                 'message' => "Import Failed! All {$totalRows} records could not be processed.",
//                 'processed' => $successCount,
//                 'unprocessed' => $errorCount,
//                 'total_rows' => $totalRows,
//                 'errors' => $errors
//             ]);
//         } elseif ($errorCount > 0 && $successCount > 0) {
          
//             return response()->json([
//                 'success' => true,
//                 'import_status' => true,
//                 'message' => "Import Partially Successful! {$successCount} records inserted successfully & {$errorCount} records failed.",
//                 'processed' => $successCount,
//                 'unprocessed' => $errorCount,
//                 'total_rows' => $totalRows,
//                 'errors' => $errors
//             ]);
//         } else {
            
//             return response()->json([
//                 'success' => true,
//                 'import_status' => true,
//                 'message' => "Import Successful! All {$successCount} records inserted successfully.",
//                 'processed' => $successCount,
//                 'unprocessed' => $errorCount,
//                 'total_rows' => $totalRows,
//                 'errors' => []
//             ]);
//         }
        
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'import_status' => false,
//             'message' => 'Bulk upload failed: ' . $e->getMessage()
//         ]);
//     }
// }

public function bulkPointManipulation(Request $request)
{
    try {
        $request->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        $file = $request->file('csvFile');
        $csvContent = file($file->getRealPath());
        
        // Log raw CSV content for debugging
        \Log::info('Raw CSV first 3 lines:', array_slice($csvContent, 0, 3));
        
        $csv = array_map('str_getcsv', $csvContent);
        
        // Log parsed CSV
        \Log::info('Parsed CSV:', ['total_rows' => count($csv), 'first_row' => $csv[0] ?? null]);
        
        // Remove BOM and skip header
        $headers = array_map(function ($header) {
            return trim(preg_replace('/^\xEF\xBB\xBF/', '', $header));
        }, $csv[0]);
        
        \Log::info('Headers:', $headers);
        
        unset($csv[0]); 
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $debugInfo = [];
        $totalRows = count($csv);
        
        // Column positions
        $PHONE_COL = 0;          
        $POINTS_EARNED_COL = 1;  
        $POINTS_REDUCED_COL = 2; 
        $DESCRIPTION_COL = 3;   
        $REMARKS_COL = 4;        
        
        foreach ($csv as $rowIndex => $row) {
            
          
            \Log::info("Processing row " . ($rowIndex + 2), ['row_data' => $row]);
            
            \DB::beginTransaction();
            
            try {
               
                if (empty(array_filter($row))) {
                    \Log::info("Skipping empty row " . ($rowIndex + 2));
                    \DB::rollback();
                    continue;
                }
                
               
                $row = array_map('trim', $row);
                
             
                $phone = isset($row[$PHONE_COL]) ? trim($row[$PHONE_COL]) : '';
                $pointsEarned = isset($row[$POINTS_EARNED_COL]) ? trim($row[$POINTS_EARNED_COL]) : '';
                $pointsReduced = isset($row[$POINTS_REDUCED_COL]) ? trim($row[$POINTS_REDUCED_COL]) : '';
                $description = isset($row[$DESCRIPTION_COL]) ? trim($row[$DESCRIPTION_COL]) : '';
                $remarks = isset($row[$REMARKS_COL]) ? trim($row[$REMARKS_COL]) : '';
                
                $debugInfo[] = [
                    'row' => $rowIndex + 2,
                    'phone_raw' => $phone,
                    'points_earned_raw' => $pointsEarned,
                    'points_reduced_raw' => $pointsReduced,
                    'description' => $description,
                    'remarks' => $remarks
                ];
                
              
                $phone = preg_replace('/[^0-9+]/', '', $phone);
                
             
                $pointsEarned = $pointsEarned !== '' ? (float) $pointsEarned : 0;
                $pointsReduced = $pointsReduced !== '' ? (float) $pointsReduced : 0;
                
                \Log::info("Row " . ($rowIndex + 2) . " parsed data:", [
                    'phone' => $phone,
                    'points_earned' => $pointsEarned,
                    'points_reduced' => $pointsReduced,
                    'description' => $description,
                    'remarks' => $remarks
                ]);
                
               
                if (empty($phone)) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Phone number is required (got empty value from column 0)";
                    $errorCount++;
                    \DB::rollback();
                    continue;
                }
                
               
                if ($pointsEarned > 0 && $pointsReduced > 0) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Both points_earned ({$pointsEarned}) and points_reduced ({$pointsReduced}) cannot be greater than 0 for phone {$phone}";
                    $errorCount++;
                    \DB::rollback();
                    continue;
                }
                
             
                if ($pointsEarned <= 0 && $pointsReduced <= 0) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Either points_earned or points_reduced must be greater than 0 for phone {$phone} (earned: {$pointsEarned}, reduced: {$pointsReduced})";
                    $errorCount++;
                    \DB::rollback();
                    continue;
                }
                
               
                $user = User::where('role', 2)
                           ->where(function($query) use ($phone) {
                               $query->where('phone', $phone)
                                     ->orWhere('phone', '+' . $phone)
                                     ->orWhere('phone', 'LIKE', '%' . substr($phone, -10));
                           })
                           ->first();
                
                \Log::info("User lookup for phone {$phone}:", ['found' => $user ? 'yes' : 'no', 'user_id' => $user?->id ?? null]);
                
                if (!$user) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Mason not found with phone {$phone}";
                    $errorCount++;
                    \DB::rollback();
                    continue;
                }
                
              
                if ($pointsEarned > 0) {
                    $input = [
                        'user_id' => $user->id,
                        'point' => $pointsEarned,
                        'is_verified' => 1,
                        'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_YES,
                        'description' => $description,
                        'remarks' => $remarks,
                    ];
                    
                    $reward = Reward::create($input);
                    
                    \Log::info("Created reward:", ['reward_id' => $reward->id]);
                    
                 
                    $logData = [
                        'table_id' => $reward->id,
                        'user_id' => \Auth::user()?->id,
                        'model_name' => 'Reward',
                        'request' => json_encode($input),
                        'response' => json_encode($reward),
                        'action' => 'create',
                        'remarks' => 'Point Added - Bulk Upload',
                    ];
                    
                    $this->createLog($logData);
                }
                
                
                if ($pointsReduced > 0) {
                 
                    if ($user->points < $pointsReduced && \Auth::user()->role != 5) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Deduct point ({$pointsReduced}) can't be greater than net point ({$user->points}) for mason {$phone}";
                        $errorCount++;
                        \DB::rollback();
                        continue;
                    }
                    
                    $input = [
                        'user_id' => $user->id,
                        'redeemed_point' => $pointsReduced,
                        'description' => $description,
                        'remarks' => $remarks,
                    ];
                    
                    $redemption = UserCatalogueRedeemtion::create($input);
                    
                    \Log::info("Created redemption:", ['redemption_id' => $redemption->id]);
                    
                 
                    $logData = [
                        'table_id' => $redemption->id,
                        'user_id' => \Auth::user()?->id,
                        'model_name' => 'UserCatalogueRedeemtion',
                        'request' => json_encode($input),
                        'response' => json_encode($redemption),
                        'action' => 'create',
                        'remarks' => 'Point Subtracted - Bulk Upload',
                    ];
                    
                    $this->createLog($logData);
                }
                
               
                $this->updatePoint($user->id);
                
              
                \DB::commit();
                $successCount++;
                
                \Log::info("Successfully processed row " . ($rowIndex + 2));
                
            } catch (\Exception $e) {
              
                \DB::rollback();
                $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                $errorCount++;
                \Log::error("Error processing row " . ($rowIndex + 2), ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
        }
        
       
        $request->session()->put('point_count', $successCount);
        
        \Log::info('Import completed:', [
            'total_rows' => $totalRows,
            'success' => $successCount,
            'errors' => $errorCount
        ]);
        
       
        if ($errorCount > 0 && $successCount == 0) {
            return response()->json([
                'success' => false,
                'import_status' => false,
                'message' => "Import Failed! All {$totalRows} records could not be processed.",
                'processed' => $successCount,
                'unprocessed' => $errorCount,
                'total_rows' => $totalRows,
                'errors' => $errors,
                'debug_info' => $debugInfo 
            ]);
        } elseif ($errorCount > 0 && $successCount > 0) {
            return response()->json([
                'success' => true,
                'import_status' => true,
                'message' => "Import Partially Successful! {$successCount} records inserted successfully & {$errorCount} records failed.",
                'processed' => $successCount,
                'unprocessed' => $errorCount,
                'total_rows' => $totalRows,
                'errors' => $errors,
                'debug_info' => $debugInfo
            ]);
        } else {
            return response()->json([
                'success' => true,
                'import_status' => true,
                'message' => "Import Successful! All {$successCount} records inserted successfully.",
                'processed' => $successCount,
                'unprocessed' => $errorCount,
                'total_rows' => $totalRows,
                'errors' => []
            ]);
        }
        
    } catch (\Exception $e) {
        \Log::error('Bulk upload exception:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false,
            'import_status' => false,
            'message' => 'Bulk upload failed: ' . $e->getMessage()
        ]);
    }
}

public function getProgresses(Request $request)
{
    if($request->session()->has('point_import'))
    {
        return response()->json([
            'success' => true,
            'import_status' => false, 
            'records' => session()->get('point_count', 0), 
            'message' => 'Importing Data. Please wait....'
        ], 200);
    }

    return response()->json([
        'success' => true, 
        'import_status' => true,
        'message' => 'Import Successful ' . session()->get('point_count', 0) . ' records processed.'
    ], 200);
}

}
