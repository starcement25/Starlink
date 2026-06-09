<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\LiftingApprovalHistory;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\Lifting;
use App\Models\Product;
use App\Models\Log;
use App\Models\UserCatalogueRedeemtion;
use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use App\Models\MasonLifting;
use App\Models\CustomerLifting;
use App\Exports\LiftingExport;
use Illuminate\Http\Request;
use App\Exports\VerifyLiftingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\DataTables\LiftingDataTable;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Lifting\CreateLiftingRequest;
use App\Http\Requests\Lifting\UpdateLiftingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
// use Illuminate\Support\Facades\Session;

class LiftingController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LiftingDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('liftings.view') ;
        //    $liftings =  Lifting::with('user')->with('Lifting')->orderByRaw('lifting.id DESC')->get();
        //    return view('admin.lifting.index', ['liftings'=> $liftings]);

        return $dataTable->render('admin.lifting.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('liftings.create') ;
        // Get Users having Role Mason = 2.
        $users    = User::where('role', 2)->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        
        $products = Product::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $users    = ['' => 'Select Mason'] + $users ;

        $dealersArr    = User::whereIn('role', ['1', '3', '4'])->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $dealers    = ['' => 'Select TE/Dealer'] + $dealersArr ;
       
        $products = ['' => 'Select User'] + $products ;
      
        return view('admin.lifting.create')
                ->with('userOption', $users)->with('userSelected', "")
                ->with('teOption', $dealers)->with('teSelected', "")
                ->with('productOption', $products)->with('productSelected', "");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateLiftingRequest $request)
    {   
        
        \Helper::checkIsUserAuthorizeToPerformTheTask('liftings.create') ;
        try {
            DB::beginTransaction();
            $request['lifting_date'] = date('d-m-Y', strtotime($request->lifting_date));
          
            $lifting = Lifting::create($request->except(['img', 'mason_id'])) ;
            
            if($request->has('img')){
                $data = $this->uploadFile($request->file('img'), 'liftings') ;
                $lifting->update(['img' => $data['path']]) ;
            }
            
            // Add Mason Lifting.
            $masonLifting =  MasonLifting::create([
                'mason_id'=> $request->mason_id,
                'lifting_id'=> $lifting->id,
            ]);

            // Add Rewards
            Reward::create([
                            'lifting_id'  => $lifting->id, 
                            'user_id'     => $request->mason_id, 
                            'bag'         => $lifting->qty, 
                            'point'       => $this->getPoint($lifting->product_id, $lifting->qty),
                            'is_verified' => 0 ,
                            'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_NO,
                            ]) ;
            DB::commit();
            Flash::success('Lifting saved successfully.');
            return redirect(route('liftings.index'));
        } 
        catch (\Exception $e) {
            DB::rollback();
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('liftings.view') ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('liftings.edit') ;
        try {
            $lifting = Lifting::with('user')->find($id);
            // return $lifting;
            if (empty($lifting)) {
                Flash::error('Lifting not found');
                return redirect(route('liftings.index'));
            }
            $lifting['lifting_date'] = date('Y-m-d', strtotime($lifting['lifting_date']));
            // Get Users having Role Mason = 2.
            $users    = User::where('id',$lifting->mason_user->mason_id)->where('role', 2)->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
            $products = Product::where('id',$lifting->product_id)->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
            $users    = ['' => 'Select User'] + $users ;
            $products = ['' => 'Select User'] + $products ;

            $dealersArr    = User::where('id',$lifting->user_id)->whereIn('role', ['1', '3', '4'])->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
            $dealers    = ['' => 'Select TE/Dealer'] + $dealersArr ;

            // return $lifting;
            return view('admin.lifting.edit')->with('lifting', $lifting)
                        ->with('userOption', $users)->with('userSelected', $lifting->mason_user->mason_id)
                        ->with('productOption', $products)
                        ->with('productSelected',  $lifting->product_id)
                        ->with('teOption', $dealers)->with('teSelected', $lifting->user_id);
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
        }
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLiftingRequest $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('liftings.edit') ;
        $logData = [
            'user_id' => Auth::user()->id,
            'request' => json_encode($request->all()),
            'action' => 'Update Lifting',
            'model_name' => 'Lifting',
        ];
        $logTable = Log::create($logData);
        try {
            
            $lifting = Lifting::find($id);

            if (empty($lifting)) {
                Flash::error('Lifting not found');
                return redirect(route('liftings.index'));
            }
            $request['lifting_date'] = date('d-m-Y', strtotime($request->lifting_date));
            DB::beginTransaction();
            $input  = $request->except(['img', 'mason_id']) ;
            $result = $lifting->update($input);

            // Update Image
            if(!empty($request->img)){
                if(file_exists(public_path($lifting->img))){
                    unlink(public_path($lifting->img));
                }
                $data = $this->uploadFile($request->file('img'), 'liftings') ;
                $lifting->update(['img' => $data['path']]) ;
            }

            //Update or Create Reward Details.
            // Reward::updateOrCreate(
            //     ['id' => $lifting->reward->id ?? null],
            //     [
            //         'lifting_id'  => $lifting->id, 
            //         'user_id'     => $request->user_id, 
            //         'bag'         => $lifting->qty, 
            //         'point'       => $this->getPoint($lifting->product_id, $lifting->qty),
            //         'is_verified' => $lifting->reward->is_verified ?? 0 ,
            // 'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_NO,
            //     ]);

            //Update or Create Mason Liftings Details.
            $masonLiftingTable = MasonLifting::updateOrCreate(
                ['id' => $lifting->mason_user->id ?? null],
                [
                    'lifting_id'  => $lifting->id, 
                    'mason_id'     => $request->mason_id, 
                    
                ]);

            // Update total user point.
            $this->updatePoint($request->mason_id);
            
            DB::commit();
            $tables = json_encode([
                'Lifting' => $lifting,
                'MasonLifting' => $masonLiftingTable,
            ]);
            $logTable->update([
                'response' => $tables
            ]);
            Flash::success('Lifting updated successfully.');
            return redirect(route('liftings.index'));

        } catch (\Exception $e) {
            DB::rollBack();
            $logTable->update([
                'response' => $e->getMessage()
            ]);
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
        }
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('liftings.delete') ;
        $logData = [
            'user_id' => Auth::user()->id,
            'request' => $id,
            'action' => 'Delete Lifting',
            'model_name' => 'Lifting',
        ];
        $logTable = Log::create($logData);
        try {
            $lifting = Lifting::with('reward')->find($id);
            if (empty($lifting)) {
                Flash::error('Lifting not found');
                return redirect(route('liftings.index'));
            }
            DB::beginTransaction();
            $masonLiftingTable = MasonLifting::where('lifting_id', $id)->get();
            if(!empty($lifting->img) && file_exists(public_path($lifting->img))){
                unlink(public_path($lifting->img));
            }
            $tables = json_encode([
                'Lifting and Reward' => $lifting,
                'MasonLifting' => $masonLiftingTable,
            ]);
            $lifting->delete();
            DB::commit();
            $logTable->update([
                'response' => $tables
            ]);
            // Update total user point.
            $this->updatePoint($lifting->reward[0]->user_id);
            
            Flash::success('Lifting deleted successfully.');
            return redirect(route('liftings.index'));
        
        } catch (\Throwable $e) {
            DB::rollBack();
            $logTable->update([
                'response' => $e->getMessage()
            ]);
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
        }
       
    }
    public function searchMason($searchVal){
        $loggedUser=Auth::user();
        if($loggedUser->role > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            $masons = User::where('name','LIKE', '%'.$searchVal.'%')->orWhere('phone','LIKE', '%'.$searchVal.'%')->where("role", 2)->whereIn('branch_id',$allocated_branches)->orderBy('name', 'DESC')->get();
        }
        else
        {
            $masons = User::where('name','LIKE', '%'.$searchVal.'%')->orWhere('phone','LIKE', '%'.$searchVal.'%')->where("role", 2)->orderBy('name', 'DESC')->get();
        } 
        return response()->json([
            'status'=> true, 'data'=> [
                "masons" => $masons
                ]
        ], 200);
    }
    public function verifyLiftings(Request $request)
    {
        if(is_file(public_path("excel_exports/automate_verify_liftings/".Carbon::now()->endOfMonth()->format("M-Y").".csv")))
        {
            $files = [
                [
                    "fileName" => Carbon::now()->endOfMonth()->format("M-Y").".csv",
                ],
                [
                    "fileName" => Carbon::now()->subMonth()->endOfMonth()->format("M-Y").".csv",
                ],
                [
                    "fileName" => Carbon::now()->subMonth(2)->endOfMonth()->format("M-Y").".csv",
                ]
            ];
        }
        else
        {
            $files = [
                [
                    "fileName" => Carbon::now()->subMonth()->endOfMonth()->format("M-Y").".csv",
                ],
                [
                    "fileName" => Carbon::now()->subMonth(2)->endOfMonth()->format("M-Y").".csv",
                ],
                [
                    "fileName" => Carbon::now()->subMonth(3)->endOfMonth()->format("M-Y").".csv",
                ]
            ];
        }
        $downloadableFiles = [];
        foreach($files as $file)
        {
            if(is_file(public_path("excel_exports/automate_verify_liftings/".$file["fileName"])))
            {
                array_push($downloadableFiles, [
                    "filePath" => asset("excel_exports/automate_verify_liftings/".$file["fileName"]),
                    "fileName" => $file["fileName"],
                ]);
            }
        }
        // return $files;
        // return $downloadableFiles;

        \Helper::checkIsUserAuthorizeToPerformTheTask('verify/lifting.view') ;
        $loggedUser=Auth::user();
        $users = null;
        if($loggedUser->role > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            $users    = User::where('role', 2)->whereIn('branch_id',$allocated_branches)->orderBy('name', 'DESC')->get();
        }
        else
        {
            $users    = User::where('role', 2)->orderBy('name', 'DESC')->get();
        }
        $user = base64_decode($request->user) ;
        $netPoint = \Helper::getUser($user)->points ?? 0;
        
        if($request->expectsJson()){
            
            if($user == "ALL"){
                if($loggedUser->role > 6)
                {
                    $liftings = Lifting::whereHas('user',function($q) use($allocated_branches){
                        $q->whereIn('branch_id',$allocated_branches);
                    })->with('product')->with('mason_user')->with('user')->with('reward')->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$request->fromDate, $request->toDate])->select(['lifting.*']);
                }
                else
                {
                    $liftings = Lifting::with('product')->with('mason_user')->with('user')->with('reward')->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$request->fromDate, $request->toDate])->select(['lifting.*']);
                }
           }
            else{
                // $liftings = Lifting::with('product')->with('reward')->where('user_id', $request->user)
                $liftings = Lifting::with('product')->with('mason_user')->with('user')->with('reward')
                ->whereIn(DB::raw("`lifting`.`id`"), function($q) use($user){
                    $q->select('lifting_id')->from('rewards')->where('user_id', $user);
                })      
                ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$request->fromDate, $request->toDate])->select(['lifting.*']);

                //$mason = User::where('id', $user)->get(); 
              }
        //     $liftings = Lifting::with('product')->with('reward')->with('user')->whereIn('id', function($q) use($user){
        //         $q->select('lifting_id')->from('rewards')->where('user_id', $user);
        //    })
        //    // $liftings = Lifting::with('product')->with('reward')->where('user_id', $request->user)
        //     ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$request->fromDate, $request->toDate]);

            return DataTables::eloquent($liftings)
            ->setRowId(function ($lifting) {
                return $lifting->id;
            })
            ->addColumn('status', function ($lifting) {
                // $status = $lifting->reward[0]->is_verified == 1 ? '<span class="badge badge-success"> Verified</span>' : '<span class="badge badge-danger"> Unverified</span>' ;
                if($lifting->reward[0]->is_verified == 1)
                {
                    $status = '<span class="badge badge-success"> Verified</span>';
                }
                else if($lifting->reward[0]->is_verified == 2)
                {
                    $status = '<span class="badge badge-danger"> Rejected </span>';
                }
                else
                {
                    $status = '<span class="badge badge-danger"> Unverified </span>';
                }
               return $status  ;
             //  return 1  ;
            })
            ->addColumn('star_saathi_status', function($lifting){
                $starSaathiStatus = '';
                $isLiftingAsmOrStarSaathi = LiftingApprovalHistory::where(["lifting_id" => $lifting->id])->count();
                if($isLiftingAsmOrStarSaathi > 0)
                {
                    if($lifting->req_status == 0)
                    {
                        $starSaathiStatus = '<span class="badge badge-primary"> Pending </span>';
                    }
                    else if($lifting->req_status == 1)
                    {
                        $starSaathiStatus = '<span class="badge badge-success"> Approved </span>';
                    }
                    else
                    {
                        $starSaathiStatus = '<span class="badge badge-danger"> Rejected </span>';
                    }
                }
                return $starSaathiStatus;
            })
            ->addColumn('available_stock', function($lifting){
                $stockStatus = '';
                $liftingQuantity =  $lifting->qty ;
                $stockAvailable =   $lifting->available_stock ;

                if(is_null($stockAvailable) || $stockAvailable == ''){
                    $stockStatus = '';
                }
                elseif($liftingQuantity  <= $stockAvailable)
                {
                    $stockStatus = '<span class="badge badge-success"> Yes </span>';
                   
                }
                elseif($liftingQuantity > $stockAvailable)
                {
                     $stockStatus = '<span class="badge badge-danger"> No </span>';
                }
               
                return $stockStatus;
            })

            ->addColumn('action_taken_at', function($lifting){
                return $lifting->req_type == 2 ? $lifting->action_taken_at : '';
            })
            ->addColumn('verified_by', function ($lifting) {
                // return $lifting->reward[0]->user->name ?? "" ;
                $userNames = [];
                $verified_by_ids = $lifting->reward[0]->verified_by_history ?? false;
                $verified_by_ids = $verified_by_ids ? json_decode($verified_by_ids) : [];
                foreach($verified_by_ids as $verified_by_id)
                {
                    if($verified_by_id == 0)
                    {
                        array_push($userNames, "Import");
                    }
                    else
                    {
                        array_push($userNames, (User::find($verified_by_id)->name ?? ""));
                    }
                }
                return (count($userNames) > 0) ? implode(", ", $userNames) : ($lifting->reward[0]->user->name ?? "");
              //  return 1  ;
            })
            ->addColumn('verified_by_at', function ($lifting) {
                // return $lifting->reward[0]->verified_by_at ?? "" ;
                $verified_by_at = $lifting->reward[0]->verified_by_at_history ?? false ;
                return $verified_by_at ? implode(", ", json_decode($verified_by_at)) : ($lifting->reward[0]->verified_by_at ?? "");
                 
              //  return 1  ;
            })
            ->addColumn('mason_submitted_qty', function ($lifting) {
                $masonSubmitedQty = '';
                if($lifting->req_type == 2)
                {
                    $masonSubmitedQty = LiftingApprovalHistory::where([
                        'lifting_id' => $lifting->id,
                        'action_status' => 0
                    ])->first()->qty ?? "";
                }
                return $masonSubmitedQty;
             
                 
              //  return 1  ;
             })
            ->addColumn('dealer_editted_qty', function ($lifting) {
                $dealerEditedQty = '';
                if($lifting->req_type == 2)
                {
                    $editedQtys = LiftingApprovalHistory::where([
                        'lifting_id' => $lifting->id,
                        'action_status' => 1,
                    ])->get();
                    foreach($editedQtys as $editedQty)
                    {
                        $user = User::find($editedQty->action_taken_by);
                        if(in_array($user->role, [3,4,6]))
                        {
                            $dealerEditedQty = $editedQty->qty;
                        }
                    }
                }
                return $dealerEditedQty;
             
                 
              //  return 1  ;
             })
            // ->addColumn('approved_qty', function ($lifting) {
            //     $approvedQty = '';
            //     if($lifting->req_type == 2)
            //     {
            //         $approvedQty = LiftingApprovalHistory::where([
            //             'lifting_id' => $lifting->id,
            //             'action_status' => 3
            //         ])->first()->qty ?? "";
            //     }
            //     return $approvedQty;
            //  })
            ->addColumn('last_modified_by', function ($lifting) {
                $lastModifiedBy = '';
                if($lifting->req_type == 2)
                {
                    $lastModifiedId = LiftingApprovalHistory::where([
                        'lifting_id' => $lifting->id,
                        'action_status' => 1
                    ])->orderBy('id', 'DESC')->first()->action_taken_by ?? "";
                    $lastModifiedBy = User::find($lastModifiedId)->roles->role_name ?? "";
                }
                return $lastModifiedBy;
             })
            ->addColumn('last_modified_date_time', function ($lifting) {
                $lastModifiedDateTime = '';
                if($lifting->req_type == 2)
                {
                    $lastModifiedDateTime = LiftingApprovalHistory::where([
                        'lifting_id' => $lifting->id,
                        'action_status' => 1
                    ])->orderBy('id', 'DESC')->first()->created_at ?? "";
                }
                return $lastModifiedDateTime;
             })
             ->addColumn('autolifting_approval', function ($lifting) {
                $autoLiftingApproval = "No";
                $isLiftingAsmOrStarSaathi = LiftingApprovalHistory::where(["lifting_id" => $lifting->id])->count();
                if($isLiftingAsmOrStarSaathi > 0 && $lifting->seek_approval == 3)
                {
                    $autoLiftingApproval = "Yes";
                }
                return $autoLiftingApproval;
             })
             ->addColumn('lifting_by', function ($lifting) {
                $lifting_by = "";
                $asm = LiftingApprovalHistory::where(['lifting_id' => $lifting->id, "action_status" => 0, "seek_approval" => 4])->orderBy("id", "DESC")->first();
                if($lifting->req_type == 1)
                {
                    $lifting_by = "OTP";
                }
                if($lifting->req_type == 2)
                {
                    $lifting_by = "Star Saathi";
                }
                if($asm != null)
                {
                    $lifting_by = "ASM - ". (User::find($asm->seek_approval_by)->name ?? "");
                }
                return $lifting_by;
             })
             ->filterColumn('lifting_id', function($query, $keyword) {
                // Remove LF and leading zeroes from the keyword
                $numericId = ltrim(str_replace('LF', '', $keyword), '0');
            
                // Apply search using the cleaned numeric ID
                $query->where('id', 'like', "%{$numericId}%");
            })
             ->addColumn('lifting_id', function ($lifting) {
                return "LF".str_pad($lifting->id,10,"0",STR_PAD_LEFT );
             })
            ->addColumn('lifting_creation_date_and_time', function ($lifting) {
                return $lifting->created_at ?? "";
             })
            ->addColumn('bd_editted_qty', function ($lifting) {
                $bdEditedQty = '';
                if($lifting->req_type == 2)
                {
                    $editedQtys = LiftingApprovalHistory::where([
                        'lifting_id' => $lifting->id,
                        'action_status' => 1,
                    ])->get();
                    foreach($editedQtys as $editedQty)
                    {
                        $user = User::find($editedQty->action_taken_by);
                        if($user->role == 1)
                        {
                            $bdEditedQty = $editedQty->qty;
                        }
                    }
                }
                return $bdEditedQty;
             
                 
              //  return 1  ;
             })
            ->editColumn('product.name', function ($lifting) {
                return $lifting->product->name ?? ""  ;
             })
            ->editColumn('reward.point', function ($lifting) {
                // Lifting may has many point & return type array.
                 $rewards = $lifting->reward ?? [] ;
                 $totalPoint = 0 ;
                foreach ($rewards as $key => $row) {
                    $totalPoint += $row['point'] ;
                }

                return $totalPoint ;
             })
            // ->addColumn('action', function ($lifting) use($netPoint){
            //     $action = $lifting->reward->is_verified ? '<span class="badge badge-success"> Verified</span>' : '' ;
            //     if($lifting->reward->point < $netPoint){
            //         return $lifting->reward->is_verified ? '<input type="checkbox"  id="switch'.$lifting->id.'" value="'.$lifting->reward->id.'" onchange="changeStatus('.$lifting->id.')" checked>' 
            //         : '<input type="checkbox"  id="switch'.$lifting->id.'" value="'.$lifting->reward->id.'" onchange="changeStatus('.$lifting->id.')">';
            //     }
               
            // })
            ->addColumn('action', function ($lifting) use($netPoint){
                
                // return $lifting->reward[0]->is_verified == 0 ? '  <a href="'.route('verify.liftings.edit', ['lifting_id'=> $lifting->id]).'" class="btn btn-default btn-xs">
                //                  <i class="far fa-edit"></i>
                //              </a> ' : '';

                return '<a href="'.route('verify.liftings.edit', ['lifting_id'=> $lifting->id]).'" class="btn btn-default btn-xs">
                            <i class="far fa-edit"></i>
                        </a>' ;
               
            })
            ->addColumn('reward.attachment',function($lifting){
                return $lifting->reward[0]->attachment == null ? "No Attachment" : "<a href='".asset($lifting->reward[0]->attachment)."' target='_blank'> Open Attachment </a>";
            })
            ->editColumn('user.name',function($lifting){
                return $lifting->user->name ?? "";
            })
            ->addColumn('mason.name',function($lifting){
                return $lifting->mason_user->user->name ?? "";
            })
            ->addColumn('mason.phone',function($lifting){
                return $lifting->mason_user->user->phone ?? "";
            })
            ->addColumn('mason.branch',function($lifting){
                return $lifting->mason_user->user->branch->name ?? "";
            })
            // ->editColumn('mason.phone',function($lifting){
            //     return $lifting->mason_user->user ?? "";
            // })
            ->editColumn('user.emp_code',function($lifting){
                return $lifting->user->emp_code ?? "";
            })
            ->editColumn('user.sap_code',function($lifting){
                return $lifting->user->sap_code ?? "";
            })
            ->addColumn('te.emp_code',function($lifting){
                return $lifting->mason_user->user->te_linked->emp_code ?? "";
            })
            ->addColumn('te.name',function($lifting){
                return $lifting->mason_user->user->te_linked->name ?? "";
            })
            ->addColumn('te.phone',function($lifting){
                return $lifting->mason_user->user->te_linked->phone ?? "";
            })
            ->addColumn('zone',function($lifting){
                return ($lifting->mason_user->user->branch->zone->name ?? "");
            })
            ->rawColumns(["action", "star_saathi_status", "status","reward.attachment", "available_stock"])
            ->toJson();
         //   return $liftings;
          
        }
        return view('admin.lifting.verify-lifting')->with([
            'users' => $users,
            'downloadableFiles' => $downloadableFiles,
        ]);
    }

    // Update Reward table is verified column in single. 
    public function updateRewardStatus(Request $request)
    {
        $request->validate([
            'is_verified' => 'required',
            'file' => Rule::when($request->is_verified == 1, ['required']),
           ]);
        if($request->is_verified != 1 && Auth::user()->role != 5)
        {
            Flash::error("You have not permission to unverified/reject a lifting.");
            return redirect()->route('verify.liftings.edit', $request->lifting_id);
        }
        $logData = [
            'user_id' => Auth::user()->id,
            'request' => json_encode($request->all()),
            'action' => 'Update Verify Lifting',
            'model_name' => 'Reward',
        ];
        $logTable = Log::create($logData);
        try 
        {
            $users = User::where('role', 2)->orderBy('name', 'DESC')->get();
            $lifting = Lifting::find($request->lifting_id) ;

            if(empty($lifting)){
                    Flash::error("No lifting records found.");
                    return redirect()->route('verify.liftings')->with('users', $users);
                    // return response()->json(['status'=>false , 'message'=> 'No lifting records found'], 200);
            }
            $reward = Reward::find($lifting->reward[0]->id) ;
            
            if(empty($reward)){
                    Flash::error("No rewards records found to that lifting.");
                    return redirect()->route('verify.liftings')->with('users', $users);
                    //return response()->json(['status'=>false , 'message'=> 'No rewards records found to that lifting.'], 200);
            }

            if($reward->is_verified == $request->is_verified)
            {
                Flash::error("Same Status cannot be update again.");
                return redirect()->back()->withInput();
            }

            if($reward->is_verified == Reward::VERIFIED)
            {
                $getMasonRedeptions = UserCatalogueRedeemtion::where("user_id", $reward->user_id)
                    ->where("status", "!=", UserCatalogueRedeemtion::STATUS_REJECTED)
                    ->where("status", "!=", UserCatalogueRedeemtion::STATUS_UNDELIVERED)
                    ->lockForUpdate()
                    ->get();

                $getMasonRedeemPoint = $getMasonRedeptions->sum("redeemed_point");

                // $getSumOfPointUptoUpdatingLiftingReward = Reward::where("user_id", $reward->user_id)->where("is_verified", Reward::VERIFIED)->where('id', "<=", );
                $getSumOfPointUptoUpdatingLiftingReward = DB::table('lifting')
                ->rightJoin('rewards', 'rewards.lifting_id', '=', 'lifting.id')
                ->where('rewards.user_id', $reward->user_id)
                ->where(function($q) use ($reward) {
                    $q->where('lifting.id', '<=', $reward->lifting_id)
                    ->orWhereNull('rewards.lifting_id');
                })
                ->where('rewards.is_verified', Reward::VERIFIED)
                ->sum('rewards.point');

                $liftingTotalPoint = $lifting->reward->sum("point");
                
                if(($getSumOfPointUptoUpdatingLiftingReward - $liftingTotalPoint) < $getMasonRedeemPoint)
                {
                    Flash::error("Lifting cannot be update as it has been used for redeemtion.");
                    return redirect()->back()->withInput();
                }
            }
            
                // Get 90% of Lifting Average.
                // $liftingAvg = $this->getLifting90($lifting->product_id, $lifting->user_id);
            
                // if(!empty($liftingAvg))
                // {
                //     if($reward->bag > $liftingAvg){
                //         Flash::error('Lifting quantity can not be greater than lifting average');
                //         return redirect(route('verify.liftings'));
                //     }
                // }

                // Get Mason Current Month Total Bag Lifting.
                // $lifting->user_id = Dealer, $reward->user_id = Mason .
                // $currentMonthBagLifting = $this->getLiftingCurrMonthMason($lifting->product_id, $lifting->user_id, $reward->user_id) ;
                // $totalLifting = $reward->bag + $currentMonthBagLifting ;
                // if(!empty($currentMonthBagLifting)) 
                // {
                //     if($totalLifting > $liftingAvg){
                //         Flash::error('Lifting quantity can not be greater than Mason Current Month Bag Lifting.');
                //         return redirect(route('verify.liftings'));
                //     }
                // }

            
                // Save Point In Reward Table
            // $reward->update(['is_verified' => $request->is_verified, 'point'=> $point]);
            // $reward->update(['is_verified' => $request->is_verified]);
            DB::beginTransaction();
                $verified_by_history = [];
                $verified_by_at_history = [];
                $reward = Reward::where('lifting_id', $request->lifting_id)->first();
                if($reward != null )
                {
                    if($reward->verified_by_history != null)
                    {
                        $verified_by_history = json_decode($reward->verified_by_history);
                    }
                    else
                    {
                        if($reward->verified_by != null)
                        {
                            array_push($verified_by_history, $reward->verified_by);
                        }
                    }
                }
                array_push($verified_by_history, Auth::user()->id);
                if($reward != null )
                {
                    if($reward->verified_by_at_history != null)
                    {
                        $verified_by_at_history = json_decode($reward->verified_by_at_history);
                    }
                    else
                    {
                        if($reward->verified_by_at != null)
                        {
                            array_push($verified_by_at_history, $reward->verified_by_at);
                        }
                    }
                }
                array_push($verified_by_at_history, Carbon::now()->format('y-m-d H:i:s'));
                //keeping Reward History
                $rewardRecords = Reward::where('lifting_id', $request->lifting_id)->get();
                foreach($rewardRecords as $rewardRecord)
                {
                    $isEligibleForLedger = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                    if(RewardHistory::where("lifting_id", $request->lifting_id)->where("is_verified", Reward::VERIFIED)->count() < 1)
                    {
                        if($rewardRecord->is_verified == Reward::VERIFIED)
                        {
                            $isEligibleForLedger = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                        }
                    }
                    else
                    {
                        $lastRewardHistoryRec = RewardHistory::where("reward_id", $rewardRecord->id)->latest("id")->first();
                       
                        if(!empty($lastRewardHistoryRec))
                        {
                            if($lastRewardHistoryRec->is_verified == Reward::VERIFIED)
                            {
                                if($rewardRecord->is_verified != Reward::VERIFIED)
                                {
                                    $isEligibleForLedger = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                                }
                            }
                            else
                            {
                                if($rewardRecord->is_verified == Reward::VERIFIED)
                                {
                                    $isEligibleForLedger = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                                }
                            }
                        }
                    }

                    RewardHistory::create([
                        'reward_id' => $rewardRecord->id,
                        'point' => $rewardRecord->point,
                        'bag' => $rewardRecord->bag,
                        'lifting_id' => $rewardRecord->lifting_id,
                        'user_id' => $rewardRecord->user_id,
                        'date' => $rewardRecord->date,
                        'is_verified' => $rewardRecord->is_verified,
                        'verified_by' => $rewardRecord->verified_by,
                        'verified_by_at' => $rewardRecord->verified_by_at,
                        'is_bonus' => $rewardRecord->is_bonus,
                        'description' => $rewardRecord->description,
                        'show_point' => $rewardRecord->show_point,
                        'is_eligible_for_ledger' => $isEligibleForLedger,
                        'reward_date_time' => $rewardRecord->updated_at,
                        'attachment' => $rewardRecord->attachment,
                        'remarks' => $rewardRecord->remarks,
                    ]);
                }
                $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                if($reward->is_verified == Reward::VERIFIED)
                {
                    // if($request->lifting_id != Reward::VERIFIED)
                    if($request->is_verified != Reward::VERIFIED)
                    {
                        $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                    }
                }
                else
                {
                   // if($request->lifting_id == Reward::VERIFIED)
                    if($request->is_verified == Reward::VERIFIED)
                    {
                        $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                    }
                }
                Reward::where('lifting_id', $request->lifting_id)->update([
                        'is_verified' => $request->is_verified,
                        'verified_by_at' => Carbon::now()->format('y-m-d H:i:s'),
                        'verified_by_at_history' => json_encode($verified_by_at_history),
                        'verified_by' => Auth::user()->id,
                        'verified_by_history' => json_encode($verified_by_history),
                        'is_eligible_for_ledger' => $isEligibleForLedgerInRewardTable,
                    ]);
                if($lifting->req_type == 2)
                {
                    if($request->is_verified == 1)
                    {
                        $lifting->update([
                            'req_status' => 1,
                            // 'action_taken_at' => Carbon::now()->format('y-m-d H:i:s')
                            'action_taken_by' => \Auth::user()->id,
                        ]);
                    }
                    else if($request->is_verified == 2)
                    {
                        $lifting->update([
                            'req_status' => 2,
                            // 'action_taken_at' => Carbon::now()->format('y-m-d H:i:s')
                            'action_taken_by' => \Auth::user()->id,
                        ]);
                    }
                    // else
                    // {
                    //     $lifting->update([
                    //         'req_status' => 0,
                    //         'action_taken_at' => null
                    //     ]);
                    // }
                    $liftingApprovalHistory = LiftingApprovalHistory::where('lifting_id', $lifting->id)->orderBy('id', 'DESC')->first();
                    $liftingApprovalHistoryActionStatus = null;
                    if(in_array($request->is_verified, [0, 1]) && $liftingApprovalHistory != null && $liftingApprovalHistory->action_status != 3)
                    {
                        $liftingApprovalHistoryActionStatus = 3;
                    }
                    else if($request->is_verified == 2 && $liftingApprovalHistory != null && $liftingApprovalHistory->action_status != 4)
                    {
                        $liftingApprovalHistoryActionStatus = 4;
                    }

                    if($liftingApprovalHistoryActionStatus != null)
                    {
                        $point = 0;
                        $bonusPoint = 0;
                        $rewards = Reward::where('lifting_id', $lifting->id)->get();
                        $teName = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->with('mason')->first();
                        $approvalWindowSettingName = $liftingApprovalHistory->seek_approval == 1 ? 'dealer/rssd_approval_window' : 'bdo_approval_window';
                        foreach($rewards as $val)
                        {
                            if($val->is_bonus == 0){ 
                                $point = $val->point; 
                            } 
                            else{ 
                                $bonusPoint = $val->point;
                            }
                        }
                        $liftingApprovalHistory = [
                            'lifting_id' => $lifting->id,
                            'qty' => $lifting->qty,
                            'point' => $point,
                            'bonus_point' => $bonusPoint,
                            'seek_approval' => $liftingApprovalHistory->seek_approval,
                            'seek_approval_by' => ($teName->mason->parent ?? 0),
                            'seek_approval_from' => $lifting->seek_approval_from,
                            'approval_window' => $this->settingVal('setting_name', $approvalWindowSettingName),
                            'action_status' => $liftingApprovalHistoryActionStatus,
                            'action_taken_by' => \Auth::user()->id,
                        ];
                        LiftingApprovalHistory::create($liftingApprovalHistory);
                    }
                }
            // if($request->is_verified == 1)
            // {
            //     Reward::where('lifting_id', $request->lifting_id)->update(['verified_by' => Auth::user()->id]);
            // }
            // Update total user point.
            $this->updatePoint($lifting->reward[0]->user_id);
        
            //$extra =  $request->is_verified == "1" ? '<span class="badge badge-success"> Verified</span>': '';
            //return response()->json(['status'=>true ,'extra'=> $extra, 'message'=> 'Liftings verified successfully.'], 200);
        
        
            // Update Image If There Is Image.
                if(!empty($request->file))
                {
                    //checking file is presemt in DB or not
                    if(!empty($reward[0]->attachment))
                    {
                        //If Previous is File Exist on Server Delete it
                        if(file_exists(public_path($reward[0]->attachment))){
                            unlink(public_path($reward[0]->attachment));
                        }
                    }
                    //Upload new File
                    $data = $this->uploadFile($request->file('file'), 'attachments') ;
                // $reward->update(['attachment' => $data['path']]) ;
                    Reward::where('lifting_id', $request->lifting_id)->update(['attachment' => $data['path']]);
                }
            DB::commit();
            $rewardTable = Reward::where('lifting_id', $request->lifting_id)->get();
            $tables = json_encode([
                'Reward' => $rewardTable
            ]);
            $logTable->update([
                'response' => $tables
            ]);
            Flash::success("Updated Successfully.");
            // return redirect()->route('verify.liftings')->with('users', $users);
            return redirect()->route('verify.liftings');
        }
        catch (Exception $e) 
        {
            $users = User::where('role', 2)->orderBy('name', 'DESC')->get();
            DB::rollBack();
            $logTable->update([
                 'response' => $e->getMessage()
             ]);
             Flash::error($e->getMessage());
             return redirect()->route('verify.liftings')->with('users', $users);
         }
    
    }

    // Update Reward table is verified column in bulk. 
    public function updateBulkRewardStatus(Request $request)
    { 
        // \Helper::checkIsUserAuthorizeToPerformTheTask('verify/lifting.edit') ;
        // $liftings = Lifting::with('product')->with('reward')->where('user_id', $request->user)
        //             ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$request->fromDate, $request->toDate])->get();
        // $rewardIds = [];
        // foreach ($liftings as $key => $lifting) {
        //     if($lifting->reward->id){
        //         $rewardIds[] = $lifting->reward->id ;
        //     }
           
        // }
        // if(count($rewardIds) > 0){
        //     Reward::whereIn('id', $rewardIds)->update(['is_verified' => 1]) ;
            
        //     // Update total user point.
        //     $this->updatePoint($request->user);

        //     return response()->json(['status'=> true , 'message'=> 'Liftings verified successfully.'], 200);
        // }

        // return response()->json(['status'=> false , 'message'=> 'No rewards records found for the liftings.'], 200);
    }

    // User Point Report Generation.
    public function masonReport(Request $request)
    {
        
    }

    public function editVerifyLiftings($liftingId)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('verify/lifting.edit') ;
        //return $liftingId;
        $lifting=Lifting::with('product')->with('reward')->where('id',$liftingId)->first();
        if(Auth::user()->role == 5)
        {
            $verificationOptions = ['' => 'Select','1' => 'Verified', '0'=> 'Unverified', '2'=> 'Rejected'];
        }
        else
        {
            $verificationOptions = ['' => 'Select','1' => 'Verified'];
        }
        //return $lifting;
        return view('admin.lifting.edit-verify-lifting')->with([
             'lifting' => $lifting,
             'verificationOptions' => $verificationOptions,
             'is_verified' => $lifting->reward[0]->is_verified,
         ]);
    }

    //Getting downloaded percentage for viewing the progress in the fornt-end
    public function getExcelDownloadingProgressPercentage()
    {
        // if(Session::has('verifyLiftingExcelDownloadingProgressPercentage'))
        // {
        //     $progressPercentage = Session::get('verifyLiftingExcelDownloadingProgressPercentage');
        //     return response()->json(['status'=> true , 'message'=> 'Verify Lifting Excel Downloading Progress Percentage fethed succesfully.', 'data' => ['progressPercentage' => $progressPercentage]], 200);
        // }
        // return response()->json(['status'=> false , 'message'=> 'Verify Lifting Excel Downloading Progress Session not found', 'data' => []], 200);
        // return response()->json(array(Session::get('progress')));
    }

    // Excel Export Of Verify Lifting Data.
    public function downloadExcel(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('verify/lifting.view') ;
        // $filename = "Verify_Liftings_".$this->getUniqueId().".csv";
        $fromDate  = $request->fromDate ?? null;
        $toDate    = $request->toDate ?? null;
        $userId    = !empty($request->user) ? base64_decode($request->user) : null;
        

        // To download directly need to return file
        // return Excel::download((new VerifyLiftingExport)->forFromDate($fromDate)->forToDate($toDate)->forUserId($userId), $file_name, null, [\Maatwebsite\Excel\Excel::XLSX]);
        if($userId == "ALL")
        {
            $query = Lifting::with('product')->with('mason_user')->with('user')->with('reward')
                    ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$fromDate, $toDate]);
        }
        else
        {
            $query = Lifting::with('product')->with('mason_user')->with('user')->with('reward')->whereIn('id', function($q) use($userId){
                $q->select('lifting_id')->from('rewards')->where('user_id', $userId);
           })->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$fromDate, $toDate]);
        }
        $numberOfRecords = $query->count();
        set_time_limit(0);
        $filename = "Verify_Liftings_".$this->getUniqueId().".csv";
        $headings = [
            'Lifting ID',
            'Date',
            'Dealer',
            'Dealer Code',
            'Dealer SAP Code',
            'Mason',
            'Mason Mobile',
            'Mason Branch',
            'TE Code',
            'TE Name',
            'TE Phone',
            'Zone',
            'Product Name',
            'Approved Quantity',
            'Mason Submitted Quantity',
            'Dealer Edited Quantity',
            'BD Edited Quantity',
            'Last Modified By',
            'Last Modified Date and Time',
            'Auto Approved',
            'Lifting By',
            'Lifting Creation Date and Time',
            'Point',
            'Attachment',
            'Status',
            'Star Saathi / ASM Status',
            'Stock Availability',
            'Action Taken At',
            'verified_by',
            'verified_at',
        ];

        $myfile = fopen(public_path("/excel_exports/verify_liftings/").$filename, "w");
        fputcsv($myfile,$headings);
        $fetchDataLimit = 1000;
        $fetchDataFrom = 0;
        $i = 0;
        $dataProcessedCount = 0;
        // Session::put('verifyLiftingExcelDownloadingProgressPercentage', 0);
        while($i < $numberOfRecords)
        {
            $data = $query->skip($fetchDataFrom)->take($fetchDataLimit)->get();
            foreach($data as $lifting)
            {
                $stockStatus = '';
                $liftingQuantity =  $lifting->qty ;
                $stockAvailable =   $lifting->available_stock ;

                if(is_null($stockAvailable) || $stockAvailable == ''){
                    $stockStatus = '';
                }
                elseif($liftingQuantity  <= $stockAvailable){
                    $stockStatus = 'Yes';
                }
                elseif($liftingQuantity > $stockAvailable){
                     $stockStatus = 'No';
                }

                $rewards = $lifting->reward ?? [] ;
                $totalPoint = 0 ;
                foreach ($rewards as $key => $row) {
                    $totalPoint += $row['point'] ;
                }
                $starSaathiStatus = '';
                $masonSubmitedQty = '';
                $dealerEditedQty = '';
                $bdEditedQty = '';
                $lastModifiedBy = '';
                $lastModifiedDateTime = '';
                $isLiftingAsmOrStarSaathi = LiftingApprovalHistory::where(["lifting_id" => $lifting->id])->get();
                $lifting_by = "";
                if($lifting->req_type == 1)
                {
                    $lifting_by = "OTP";
                }
                if($lifting->req_type == 2)
                {
                    $lifting_by = "Star Saathi";
                }
                $countOfIsLiftingAsmOrStarSaathi = count($isLiftingAsmOrStarSaathi);
                if($countOfIsLiftingAsmOrStarSaathi > 0)
                {
                    if($lifting->req_status == 0)
                    {
                        $starSaathiStatus = 'Pending';
                    }
                    else if($lifting->req_status == 1)
                    {
                        $starSaathiStatus = 'Approved';
                    }
                    else
                    {
                        $starSaathiStatus = 'Rejected';
                    }
                    if($lifting->req_type == 2)
                    {
                        foreach($isLiftingAsmOrStarSaathi as $val)
                        {
                            if($val->action_status == 0)
                            {
                                if($val->seek_approval == 4)
                                {
                                    $lifting_by = "ASM - ". (User::find($val->seek_approval_by)->name ?? "");
                                }
                                $masonSubmitedQty = $val->qty;
                            }
                            else if($val->action_status == 1)
                            {
                                $user = User::find($val->action_taken_by);
                                $lastModifiedRecord = $val;
                                if($user->role == 1)
                                {
                                    $bdEditedQty = $val->qty;
                                }
                                else if(in_array($user->role, [3,4,6]))
                                {
                                    $dealerEditedQty = $val->qty;
                                }
                            }
                            else if($val->action_status == 3)
                            {

                            }
                        }
                        $lastModifiedBy = ($user->roles->role_name ?? "");
                        $lastModifiedDateTime = ($lastModifiedRecord->created_at ?? "");
                    }
                }
                $attachment = ($lifting->reward[0]->attachment ?? "");
                if($attachment == null)
                {
                    $attachment = null;
                }
                else
                {
                    $attachment = asset($attachment);
                }
                $autoLiftingApproval = "No";
                if($countOfIsLiftingAsmOrStarSaathi > 0 && $lifting->seek_approval == 3)
                {
                    $autoLiftingApproval = "Yes";
                }

                //-------------- Verified By Details Lifting ---------------------
                $userNames = [];
                $verified_by_ids = $lifting->reward[0]->verified_by_history ?? false;
                $verified_by_ids = $verified_by_ids ? json_decode($verified_by_ids) : [];
                foreach($verified_by_ids as $verified_by_id)
                {
                    if($verified_by_id == 0)
                    {
                        array_push($userNames, "Import");
                    }
                    else
                    {
                        array_push($userNames, (User::find($verified_by_id)->name ?? ""));
                    }
                }
                $verifiedBy = (count($userNames) > 0) ? implode(", ", $userNames) : ($lifting->reward[0]->user->name ?? "");

                // ------------------------ Verified By At History -----------------------------------
                $verified_by_at = $lifting->reward[0]->verified_by_at_history ?? false ;
                // $verifiedByAtHistory = $verified_by_at ? implode(", ", json_decode($verified_by_at)) : ($lifting->reward[0]->verified_by_at ?? "");

                if ($verified_by_at) {
                    $dates = json_decode($verified_by_at, true);
                    $formattedDates = [];

                    if (is_array($dates)) {
                        foreach ($dates as $val) {
                            $formattedDates[] = $this->formatDateFlexible($val);
                        }
                    }

                    $verified_by_at_history = implode(", ", $formattedDates);
                } else {
                    $singleDate = $lifting->reward[0]->verified_by_at ?? "";
                    $verified_by_at_history = $this->formatDateFlexible($singleDate);
                }

                $content = [
                    "LF".str_pad($lifting->id,10,"0",STR_PAD_LEFT ),
                    $lifting->lifting_date ?? "",
                    $lifting->user->name ?? "",
                    $lifting->user->emp_code ?? "",
                    $lifting->user->sap_code ?? "",
                    $lifting->mason_user->user->name ?? "",
                    $lifting->mason_user->user->phone ?? "",
                    $lifting->mason_user->user->branch->name ?? "",
                    $lifting->mason_user->user->te_linked->emp_code ?? "",
                    $lifting->mason_user->user->te_linked->name ?? "",
                    $lifting->mason_user->user->te_linked->phone ?? "",
                    $lifting->mason_user->user->branch->zone->name ?? "",
                    $lifting->product->name ?? "",
                    $lifting->qty ?? "",
                    $masonSubmitedQty,
                    $dealerEditedQty,
                    $bdEditedQty,
                    $lastModifiedBy,
                    $lastModifiedDateTime,
                    $autoLiftingApproval,
                    $lifting_by,
                    $lifting->created_at ?? "",
                    $totalPoint ?? "",
                    $attachment,
                    ($lifting->reward[0]->is_verified ?? "") == 1 ? 'Verified' : (($lifting->reward[0]->is_verified ?? "") == 2 ? 'Rejected' : 'Unverified'),
                    $starSaathiStatus,
                    $stockStatus,
                    $lifting->req_type == 2 ? $lifting->action_taken_at : '',
                   // $lifting->reward[0]->user->name ?? "",
                   $verifiedBy,
                  // $lifting->reward[0]->verified_by_at ?? "",
                  $verified_by_at_history,
                ];
                fputcsv($myfile,$content);
                $dataProcessedCount++;
                $progressPercentageValue = round(round($dataProcessedCount / $numberOfRecords, 2) * 100, 2);
                // Session::put('verifyLiftingExcelDownloadingProgressPercentage', $progressPercentageValue);
                // Session::save();
            }
            $fetchDataFrom += $fetchDataLimit;
            $i += $fetchDataLimit;
        }
        // Session::forget('verifyLiftingExcelDownloadingProgressPercentage');
        fclose($myfile);
        $filePath = public_path("/excel_exports/verify_liftings/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function formatDateFlexible($date)
    {
        if (empty($date)) {
            return "";
        }

        $date = trim($date);

        try {
            // Handle 2-digit year manually
            $parts = explode(' ', $date);

            if (count($parts) == 2) {
                [$d, $t] = $parts;
                [$y, $m, $day] = explode('-', $d);

                if (strlen($y) == 2) {
                    $y = '20' . $y; // convert 24 → 2024
                    $date = $y . '-' . $m . '-' . $day . ' ' . $t;
                }
            }

            return Carbon::parse($date)->format('d-m-Y H:i');

        } catch (\Exception $e) {
            return $date;
        }
    }

    // public function downloadExcel(Request $request)
    // {
    //     \Helper::checkIsUserAuthorizeToPerformTheTask('verify/lifting.view') ;
    //     // $filename = "Verify_Liftings_".$this->getUniqueId().".csv";
    //     $fromDate  = $request->fromDate ?? null;
    //     $toDate    = $request->toDate ?? null;
    //     $userId    = $request->user ?? null;
    //     $filePath =  $request->filePath ?? null;
    //     $fetchDataFrom = $request->fetchDataFrom ?? 0;
    //     $numberOfRecords = $request->numberOfRecords ?? 0;
    //     $query = $request->query ?? null;
    //     $fetchDataLimit = 250;

    //     // To download directly need to return file
    //     // return Excel::download((new VerifyLiftingExport)->forFromDate($fromDate)->forToDate($toDate)->forUserId($userId), $file_name, null, [\Maatwebsite\Excel\Excel::XLSX]);
    //     // if($query == null)
    //     // {
    //         if($userId == "ALL")
    //         {
    //             $query = Lifting::with('product')->with('mason_user')->with('user')->with('reward')
    //                     ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$fromDate, $toDate]);
    //         }
    //         else
    //         {
    //             $query = Lifting::with('product')->with('mason_user')->with('user')->with('reward')->whereIn('id', function($q) use($userId){
    //                 $q->select('lifting_id')->from('rewards')->where('user_id', $userId);
    //         })->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$fromDate, $toDate]);
    //         }
    //     // }
    //     // if($numberOfRecords == 0)
    //     // {
    //         $numberOfRecords = $query->count();
    //     // }
    //     set_time_limit(0);
    //     if($filePath == null)
    //     {
    //         $filename = "Verify_Liftings_".$this->getUniqueId().".csv";

    //         $myfile = fopen(public_path("/excel_exports/verify_liftings/").$filename, "w");
    //         $headings = [
    //             'Lifting ID',
    //             'Date',
    //             'Dealer',
    //             'Dealer Code',
    //             'Dealer SAP Code',
    //             'Mason',
    //             'Mason Mobile',
    //             'Mason Branch',
    //             'TE Code',
    //             'TE Name',
    //             'TE Phone',
    //             'Zone',
    //             'Product Name',
    //             'Approved Quantity',
    //             'Mason Submitted Quantity',
    //             'Dealer Edited Quantity',
    //             'BD Edited Quantity',
    //             'Last Modified By',
    //             'Last Modified Date and Time',
    //             'Auto Approved',
    //             'Lifting By',
    //             'Lifting Creation Date and Time',
    //             'Point',
    //             'Attachment',
    //             'Status',
    //             'Star Saathi / ASM Status',
    //             'Action Taken At',
    //             'verified_by',
    //             'verified_at',
    //         ];
    //         fputcsv($myfile,$headings);
    //     }
    //     else
    //     {
    //         $myfile = fopen($filePath, "a");
    //     }
        
    //     // $i = 0;
    //     $i = $fetchDataFrom;
    //     // $dataProcessedCount = 0;
    //     // Session::put('verifyLiftingExcelDownloadingProgressPercentage', 0);
    //     // while($i < $numberOfRecords)
    //     // {
    //         $data = $query->skip($fetchDataFrom)->take($fetchDataLimit)->get();
    //         foreach($data as $lifting)
    //         {
    //             $rewards = $lifting->reward ?? [] ;
    //             $totalPoint = 0 ;
    //             foreach ($rewards as $key => $row) {
    //                 $totalPoint += $row['point'] ;
    //             }
    //             $starSaathiStatus = '';
    //             $masonSubmitedQty = '';
    //             $dealerEditedQty = '';
    //             $bdEditedQty = '';
    //             $lastModifiedBy = '';
    //             $lastModifiedDateTime = '';
    //             $isLiftingAsmOrStarSaathiQuery = LiftingApprovalHistory::where(["lifting_id" => $lifting->id]);
    //             $isLiftingAsmOrStarSaathi = $isLiftingAsmOrStarSaathiQuery->get();
    //             $asm = LiftingApprovalHistory::where(['lifting_id' => $lifting->id, "action_status" => 0, "seek_approval" => 4])->orderBy("id", "DESC")->first();
    //             $lifting_by = "";
    //             if($lifting->req_type == 1)
    //             {
    //                 $lifting_by = "OTP";
    //             }
    //             if($lifting->req_type == 2)
    //             {
    //                 $lifting_by = "Star Saathi";
    //             }
    //             if($asm != null)
    //             {
    //                 $lifting_by = "ASM - ". (User::find($asm->seek_approval_by)->name ?? "");
    //             }
    //             $countOfIsLiftingAsmOrStarSaathi = count($isLiftingAsmOrStarSaathi);
    //             if($countOfIsLiftingAsmOrStarSaathi > 0)
    //             {
    //                 if($lifting->req_status == 0)
    //                 {
    //                     $starSaathiStatus = 'Pending';
    //                 }
    //                 else if($lifting->req_status == 1)
    //                 {
    //                     $starSaathiStatus = 'Approved';
    //                 }
    //                 else
    //                 {
    //                     $starSaathiStatus = 'Rejected';
    //                 }
    //                 if($lifting->req_type == 2)
    //                 {
    //                     foreach($isLiftingAsmOrStarSaathi as $val)
    //                     {
    //                         if($val->action_status == 0)
    //                         {
    //                             if($val->seek_approval == 4)
    //                             {
    //                                 $lifting_by = "ASM - ". (User::find($val->seek_approval_by)->name ?? "");
    //                             }
    //                             $masonSubmitedQty = $val->qty;
    //                         }
    //                         else if($val->action_status == 1)
    //                         {
    //                             $user = User::find($val->action_taken_by);
    //                             $lastModifiedRecord = $val;
    //                             if($user->role == 1)
    //                             {
    //                                 $bdEditedQty = $val->qty;
    //                             }
    //                             else if(in_array($user->role, [3,4,6]))
    //                             {
    //                                 $dealerEditedQty = $val->qty;
    //                             }
    //                         }
    //                         else if($val->action_status == 3)
    //                         {

    //                         }
    //                     }
    //                     $lastModifiedBy = $user->roles->role_name ?? "";
    //                     $lastModifiedDateTime = $lastModifiedRecord->created_at ?? "";
    //                 }
    //             }
    //             $attachment = $lifting->reward[0]->attachment ?? "";
    //             if($attachment == null)
    //             {
    //                 $attachment = null;
    //             }
    //             else
    //             {
    //                 $attachment = asset($attachment);
    //             }
    //             $autoLiftingApproval = "No";
    //             if($countOfIsLiftingAsmOrStarSaathi > 0 && $lifting->seek_approval == 3)
    //             {
    //                 $autoLiftingApproval = "Yes";
    //             }
    //             $userNames = [];
    //             $verified_by_ids = $lifting->reward[0]->verified_by_history ?? false;
    //             $verified_by_ids = $verified_by_ids ? json_decode($verified_by_ids) : [];
    //             foreach($verified_by_ids as $verified_by_id)
    //             {
    //                 if($verified_by_id == 0)
    //                 {
    //                     array_push($userNames, "Import");
    //                 }
    //                 else
    //                 {
    //                     array_push($userNames, (User::find($verified_by_id)->name ?? ""));
    //                 }
    //             }
    //             $verified_by_at = $lifting->reward[0]->verified_by_at_history ?? false ;
    //             $content = [
    //                 "LF".str_pad($lifting->id,10,"0",STR_PAD_LEFT ),
    //                 $lifting->lifting_date ?? "",
    //                 $lifting->user->name ?? "",
    //                 $lifting->user->emp_code ?? "",
    //                 $lifting->user->sap_code ?? "",
    //                 $lifting->mason_user->user->name ?? "",
    //                 $lifting->mason_user->user->phone ?? "",
    //                 $lifting->mason_user->user->branch->name ?? "",
    //                 $lifting->mason_user->user->te_linked->emp_code ?? "",
    //                 $lifting->mason_user->user->te_linked->name ?? "",
    //                 $lifting->mason_user->user->te_linked->phone ?? "",
    //                 $lifting->mason_user->user->branch->zone->name ?? "",
    //                 $lifting->product->name ?? "",
    //                 $lifting->qty ?? "",
    //                 $masonSubmitedQty,
    //                 $dealerEditedQty,
    //                 $bdEditedQty,
    //                 $lastModifiedBy,
    //                 $lastModifiedDateTime,
    //                 $autoLiftingApproval,
    //                 $lifting_by,
    //                 $lifting->created_at ?? "",
    //                 $totalPoint ?? "",
    //                 $attachment,
    //                 ($lifting->reward[0]->is_verified ?? "") == 1 ? 'Verified' : (($lifting->reward[0]->is_verified ?? "") == 2 ? 'Rejected' : 'Unverified'),
    //                 $starSaathiStatus,
    //                 $lifting->req_type == 2 ? $lifting->action_taken_at : '',
    //                 (count($userNames) > 0) ? implode(", ", $userNames) : ($lifting->reward[0]->user->name ?? ""),
    //                 $verified_by_at ? implode(", ", json_decode($verified_by_at)) : ($lifting->reward[0]->verified_by_at ?? ""),
    //             ];
    //             fputcsv($myfile,$content);
    //             // $dataProcessedCount++;
    //             // $progressPercentageValue = round(round($dataProcessedCount / $numberOfRecords, 2) * 100, 2);
    //             // Session::put('verifyLiftingExcelDownloadingProgressPercentage', $progressPercentageValue);
    //             // Session::save();
    //         }
    //         $fetchDataFrom += $fetchDataLimit;
    //         $i += $fetchDataLimit;
    //     // }
    //     // Session::forget('verifyLiftingExcelDownloadingProgressPercentage');
    //     fclose($myfile);
    //     if($filePath == null)
    //     {
    //         $filePath = public_path("/excel_exports/verify_liftings/".$filename);
    //     }
    //     $numberOfDownloadCompletion = round(round($i / $numberOfRecords, 2) * 100, 2);
    //     if($numberOfDownloadCompletion > 100)
    //     {
    //         $numberOfDownloadCompletion = 100;
    //     }
    //     if($i < $numberOfRecords)
    //     {
    //         return response()->json([
    //             "isDownloadComplete" => false, 
    //             "filePath" => $filePath, 
    //             "fetchDataFrom" => $fetchDataFrom, 
    //             "query" => $query, 
    //             "numberOfRecords" => $numberOfRecords, 
    //             "numberOfDownloadCompletion" => $numberOfDownloadCompletion
    //         ]);
    //     }
    //     // return response()->download($filePath)->deleteFileAfterSend(true);
    //     return response()->json([
    //         "isDownloadComplete" => true, 
    //         "filePath" => $filePath, 
    //         "numberOfDownloadCompletion" => $numberOfDownloadCompletion
    //     ]);
    // }

    //download a file file from given path and after that delete the filr
    public function downloadFileFromPath(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('verify/lifting.view') ;
        // $filename = "Verify_Liftings_".$this->getUniqueId().".csv";
        $filePath  = $request->filePath ?? null;
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function liftingExport() 
    {
        $numberOfRecords = Lifting::count();
        set_time_limit(0);
        $filename = "Lifting_History_".$this->getUniqueId().".csv";
        $headings = [
            'Lifting Date',
            'Dealer Code',
            'Dealer',
            'Mason',
            'Mason Mobile',
            'Mason Branch',              
            'Product Name',              
            'Qty',              
            'Remark',
        ];
        
        $myfile = fopen(public_path("/excel_exports/lifting_history/").$filename, "w");
        fputcsv($myfile,$headings);
        $fetchDataLimit = 1000;
        $fetchDataFrom = 0;
        $i = 0;
        while($i < $numberOfRecords)
        {
            $data = Lifting::with(['product' => function($q){
                        $q->select('id', 'name');
                    }])->with(['user'=> function($query){
                        $query->select('id', 'name','emp_code');
                    }])->with(['mason_user','mason_user.user','mason_user.user.branch'])
                    ->orderBy('id', 'DESC')->skip($fetchDataFrom)->take($fetchDataLimit)->get();
            foreach($data as $val)
            {
                $content = [
                    $val->lifting_date ?? "",
                    $val->user->emp_code ?? "",
                    $val->user->name ?? "",
                    $val->mason_user->user->name ?? "",
                    $val->mason_user->user->phone ?? "",
                    $val->mason_user->user->branch->name ?? "",
                    $val->product->name ?? "",
                    $val->qty ?? "",
                    $val->remark ?? "",
                ];
                fputcsv($myfile,$content);
            }
            $fetchDataFrom += $fetchDataLimit;
            $i += $fetchDataLimit;
        }
        fclose($myfile);
        $filePath = public_path("/excel_exports/lifting_history/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
        // return Excel::download(new LiftingExport, 'Liftings.xlsx');
        //return (new CustomerLiftingExport)->store('CustomerLifting.xlsx');
    }

    // START correcting error unverified to verified
    public function wrongLiftings()
    {
        
        $monthStartDate = date_create_from_format("d-m-Y", "01-07-2023")->format("Y-m-d");
        $monthEndDate = date_create_from_format("d-m-Y", "31-07-2023")->format("Y-m-d");
        // $dealer=Lifting::whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') between '{$monthStartDate}' and '{$monthEndDate}'")->groupBy('user_id')->pluck('user_id')->toArray();
        // dd(in_array(20327,$dealer));
        $lifting_ids = Lifting::whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') between '{$monthStartDate}' and '{$monthEndDate}'")->pluck('id')->toArray();
        // dd($lifting_ids);
        $lifting_ids = Reward::whereIn('lifting_id',$lifting_ids)->where(['is_verified'=>0,'is_bonus'=>0])->pluck('lifting_id')->toArray();
        // dd($lifting_ids);
        // return count($lifting_ids);
        $ids=[];
        
        foreach($lifting_ids as $val)
        {
            
            $lifting_id = Lifting::find($val);
            $reward_ids = Reward::where('lifting_id',$val)->pluck('id')->toArray();
            // dd($reward_ids);
            $temp = 0;
            $is_verified = 1;
            $resavg =  LiftingController::getLiftingAvg($lifting_id->product_id, $lifting_id->user_id);
            // return response()->json(['status' => true, 'msg' => 'helloo '.$resavg]);
           
        //    if($resavg <= $lifting_id->qty )
        //     {
                
        //         $is_verified = 0;
                
        //     }
            $dealerAvailableStock =  LiftingController::getLifting90($lifting_id->product_id, $lifting_id->user_id);
            $currentMonthLiftings =  LiftingController::getCurrentMonthLifting($lifting_id->product_id, $lifting_id->user_id) ;
            if(($dealerAvailableStock - $currentMonthLiftings) < $lifting_id->qty)
            {
                
                $is_verified = 0;
              // return response()->json(['status'=> false, 'msg' => "you can not add more than  90%  of the avg data of the previous month", 'data' => []]);
            }
            // foreach()
            foreach($reward_ids as $reward_id)
            {
                $reward = Reward::find($reward_id);
                $reward->is_verified = $is_verified;
                $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                if($is_verified == Reward::VERIFIED)
                {
                    $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                }
                $reward->is_eligible_for_ledger = $isEligibleForLedgerInRewardTable;
                $reward->save();
            }
            $ids[]=$lifting_id->id;
        }
        return $ids;
    }  

    public static function getLiftingAvg($product_id='', $dealer_id='')
    {
        // return "ok";
        // find the 3 month before month and years
         $curr = '07-2023';
         $month1 = '05-2023';
         $month2 = '04-2023';
        // return  $month2;
         $arr1 = explode("-",$month1);
         $arr2 = explode("-",$month2);
    
         $years=array($arr1[1],$arr2[1]);
        $marr1 = ltrim($arr1[0],'0');
        $marr2 = ltrim($arr2[0],'0');    
         $months=array($marr1,$marr2);
  
        // dd($months);
         // echo $arr1[1];
        // find the 3 month before month and years
    
       // $datas = CustomerLifting::whereIn('year', $years)->whereIn('month', $months)->where('dealer_id', $dealer_id)->where('product_id', $product_id)->sum('quantity');  
        
        $datas1 = CustomerLifting::where('year', $years[0])->where('month', $months[0])
        ->where('dealer_id', $dealer_id)
        ->where('product_id', $product_id)->sum('quantity');  
        
        $datas2 = CustomerLifting::where('year', $years[1])->where('month', $months[1])
        ->where('dealer_id', $dealer_id)
        ->where('product_id', $product_id)->sum('quantity');  
        
        $datas = $datas1+$datas2;
      
    
        // dd($datas);
        // $liftcount  = Lifting::where('user_id', $dealer_id)->count();
        //dd( $datas);
        if($datas){           
            $avglifts = $datas/2;
            return $avglifts;
        }        
        return null ;
    }

    public function updateBulkLiftings()
    {
        return "func blocked by dev jayanta.";
        set_time_limit(0);
        $is_verified = 2;
        $errMsg = [];
        if($is_verified != 1 && Auth::user()->role != 5)
        {
            return "You have not permission to unverified/reject a lifting.";
        }
        $lifting_ids = ["LF0000091416", "LF0000091417", "LF0000091418", "LF0000091419", "LF0000091420", "LF0000091421", "LF0000091422", "LF0000091424", "LF0000091426", "LF0000091500", "LF0000091503", "LF0000091517", "LF0000091519", "LF0000091520", "LF0000091525", "LF0000091526", "LF0000091559", "LF0000091561", "LF0000091563", "LF0000091564", "LF0000091574", "LF0000091575", "LF0000091578", "LF0000091601", "LF0000091602", "LF0000091603", "LF0000091604", "LF0000091605", "LF0000091606", "LF0000091607", "LF0000091611", "LF0000091619", "LF0000091627", "LF0000091628", "LF0000091632", "LF0000091640", "LF0000091641", "LF0000091642", "LF0000091665", "LF0000091669", "LF0000091677", "LF0000091697", "LF0000091747", "LF0000091811", "LF0000091822", "LF0000091848", "LF0000091849", "LF0000091879", "LF0000091880", "LF0000091881", "LF0000091883", "LF0000091884", "LF0000091886", "LF0000091887", "LF0000091917", "LF0000091958", "LF0000091959", "LF0000091961", "LF0000091994", "LF0000091997", "LF0000091998", "LF0000092001", "LF0000092004", "LF0000092008", "LF0000092021", "LF0000092022", "LF0000092029", "LF0000092032", "LF0000092034", "LF0000092035", "LF0000092076", "LF0000092099", "LF0000092105", "LF0000092162", "LF0000092163", "LF0000092199", "LF0000092240", "LF0000092245", "LF0000092247", "LF0000092248", "LF0000092344", "LF0000092356", "LF0000092403", "LF0000092413", "LF0000092419", "LF0000092500", "LF0000092744", "LF0000092749", "LF0000092791", "LF0000092813", "LF0000092922", "LF0000092923", "LF0000092927", "LF0000092928", "LF0000092936", "LF0000092937", "LF0000092940", "LF0000092942", "LF0000092945", "LF0000092946", "LF0000092947", "LF0000093057", "LF0000093092", "LF0000093355", "LF0000093508", "LF0000093512", "LF0000093529", "LF0000093783", "LF0000093967", "LF0000093978", "LF0000094008", "LF0000094279", "LF0000094281", "LF0000094436", "LF0000094437", "LF0000094438", "LF0000094484", "LF0000094487", "LF0000094591", "LF0000094907", "LF0000094910", "LF0000095097", "LF0000095544", "LF0000095546", "LF0000095547", "LF0000095549", "LF0000096057", "LF0000096058", "LF0000096061", "LF0000096063", "LF0000096064", "LF0000096068", "LF0000096239", "LF0000096355", "LF0000096357", "LF0000096532", "LF0000096534", "LF0000096564", "LF0000096915", "LF0000097483", "LF0000097875", "LF0000098114", "LF0000098115", "LF0000098118", "LF0000098242", "LF0000098248", "LF0000098681", "LF0000098682", "LF0000098932", "LF0000098965", "LF0000099125", "LF0000099136", "LF0000099140", "LF0000099142", "LF0000099162", "LF0000099172", "LF0000099173", "LF0000099174", "LF0000099175", "LF0000099176", "LF0000099178", "LF0000099179", "LF0000099180", "LF0000099182", "LF0000099184", "LF0000099191", "LF0000099193", "LF0000099194", "LF0000099195", "LF0000099198", "LF0000099609", "LF0000099610", "LF0000099611", "LF0000099613", "LF0000099773", "LF0000099790", "LF0000099792", "LF0000099957", "LF0000099999", "LF0000100702", "LF0000100703", "LF0000100705", "LF0000100709", "LF0000100714", "LF0000100851", "LF0000101167", "LF0000101345", "LF0000101487", "LF0000101489", "LF0000101490", "LF0000101602", "LF0000101605", "LF0000101610", "LF0000101618", "LF0000101620", "LF0000101629", "LF0000101630", "LF0000101641", "LF0000101647", "LF0000101821", "LF0000101829", "LF0000101830", "LF0000101832", "LF0000084457", "LF0000083536", "LF0000083540", "LF0000083549", "LF0000083551", "LF0000083552", "LF0000083554", "LF0000083555", "LF0000083556", "LF0000083558", "LF0000083559", "LF0000086428", "LF0000086431", "LF0000086433", "LF0000086434", "LF0000086435", "LF0000086436", "LF0000086438", "LF0000086439", "LF0000086640", "LF0000086646", "LF0000089040", "LF0000089044", "LF0000089045", "LF0000089048", "LF0000089049", "LF0000089056", "LF0000089057", "LF0000089095", "LF0000089096", "LF0000089336", "LF0000089371", "LF0000089372", "LF0000090019", "LF0000090020", "LF0000090021", "LF0000090022", "LF0000090023", "LF0000090024", "LF0000090026", "LF0000090188", "LF0000083580", "LF0000083584", "LF0000083585", "LF0000083597", "LF0000083598", "LF0000083599", "LF0000083600", "LF0000083601", "LF0000083602", "LF0000083617", "LF0000083628", "LF0000083521", "LF0000083522", "LF0000083523", "LF0000083524", "LF0000083526", "LF0000083528", "LF0000083530", "LF0000083532", "LF0000083567", "LF0000083572", "LF0000083671", "LF0000084653", "LF0000084696", "LF0000084105", "LF0000084106", "LF0000084107", "LF0000084108", "LF0000084114", "LF0000084115", "LF0000084116", "LF0000084118", "LF0000084123", "LF0000084128", "LF0000084129", "LF0000085590", "LF0000085591", "LF0000085594", "LF0000085597", "LF0000085599", "LF0000085601", "LF0000085603", "LF0000085607", "LF0000085609", "LF0000085610", "LF0000085612", "LF0000093245", "LF0000093246", "LF0000093247", "LF0000093248", "LF0000093249", "LF0000093250", "LF0000093251", "LF0000093253", "LF0000093254", "LF0000093255", "LF0000093257", "LF0000093258", "LF0000093259", "LF0000086926", "LF0000086928", "LF0000086934", "LF0000086935", "LF0000086936", "LF0000086937", "LF0000086938", "LF0000086940", "LF0000086942", "LF0000086943", "LF0000086945", "LF0000086947", "LF0000086949", "LF0000086950", "LF0000083809", "LF0000083817", "LF0000083826", "LF0000083833", "LF0000083834", "LF0000083842", "LF0000083854", "LF0000083865", "LF0000083892", "LF0000083894", "LF0000083936", "LF0000083948", "LF0000084100", "LF0000083779", "LF0000083793", "LF0000083798", "LF0000083807", "LF0000083814", "LF0000083824", "LF0000084181", "LF0000084216", "LF0000084518", "LF0000099205", "LF0000099207", "LF0000099209", "LF0000099211", "LF0000099212", "LF0000099214", "LF0000099220", "LF0000099221", "LF0000099222", "LF0000099223", "LF0000099225", "LF0000099227", "LF0000099228", "LF0000099230", "LF0000099232", "LF0000084085", "LF0000083592", "LF0000083594", "LF0000083595", "LF0000083596", "LF0000083618", "LF0000083619", "LF0000083621", "LF0000083629", "LF0000083632", "LF0000083635", "LF0000083641", "LF0000083642", "LF0000083643", "LF0000083650", "LF0000083655", "LF0000083656", "LF0000083660", "LF0000083664", "LF0000084046", "LF0000084924", "LF0000084927", "LF0000084932", "LF0000084944", "LF0000084967", "LF0000084968", "LF0000084969", "LF0000084970", "LF0000084972", "LF0000084974", "LF0000084976", "LF0000084978", "LF0000084980", "LF0000084983", "LF0000084984", "LF0000084985", "LF0000084986", "LF0000084987", "LF0000084988", "LF0000086247", "LF0000086251", "LF0000086254", "LF0000086256", "LF0000086258", "LF0000086262", "LF0000086264", "LF0000086266", "LF0000086268", "LF0000086270", "LF0000086272", "LF0000086274", "LF0000086276", "LF0000086278", "LF0000086280", "LF0000086282", "LF0000086285", "LF0000086288", "LF0000086289", "LF0000086290", "LF0000086314", "LF0000086315", "LF0000086263", "LF0000086269", "LF0000086277", "LF0000086279", "LF0000086283", "LF0000086284", "LF0000086286", "LF0000086291", "LF0000087197", "LF0000084648", "LF0000084649", "LF0000084259", "LF0000084260", "LF0000084261", "LF0000084262", "LF0000084263", "LF0000084264", "LF0000084265", "LF0000084266", "LF0000084279", "LF0000084280", "LF0000084281", "LF0000084282", "LF0000084283", "LF0000084284", "LF0000084285", "LF0000084286", "LF0000084288", "LF0000084289", "LF0000084291", "LF0000084292", "LF0000084294", "LF0000084296", "LF0000084298", "LF0000084317", "LF0000085080", "LF0000083574", "LF0000083575", "LF0000083583", "LF0000083586", "LF0000083587", "LF0000083589", "LF0000083590", "LF0000083603", "LF0000083604", "LF0000083605", "LF0000083606", "LF0000083607", "LF0000083608", "LF0000083614", "LF0000083615", "LF0000083616", "LF0000083620", "LF0000083622", "LF0000083623", "LF0000083630", "LF0000083631", "LF0000083633", "LF0000083634", "LF0000083636", "LF0000083637", "LF0000083638", "LF0000083639", "LF0000083644", "LF0000083647", "LF0000083648", "LF0000083649", "LF0000083651", "LF0000083652", "LF0000083653", "LF0000083654", "LF0000083657", "LF0000083658", "LF0000083659", "LF0000083661", "LF0000083662", "LF0000083663", "LF0000083665", "LF0000083666", "LF0000083667", "LF0000083668", "LF0000083669", "LF0000084044", "LF0000084048", "LF0000084050", "LF0000084055", "LF0000084056", "LF0000084058", "LF0000084062", "LF0000084064", "LF0000084066", "LF0000084067", "LF0000084070", "LF0000084341", "LF0000084342", "LF0000084343", "LF0000084344", "LF0000084345", "LF0000084346", "LF0000085059", "LF0000085098", "LF0000084353", "LF0000084331", "LF0000084383", "LF0000085266", "LF0000087337", "LF0000086596", "LF0000084311", "LF0000084313", "LF0000083674", "LF0000085898", "LF0000083742", "LF0000083770", "LF0000088035", "LF0000083749", "LF0000083856", "LF0000084399", "LF0000084124", "LF0000083677", "LF0000084356", "LF0000086159", "LF0000084668", "LF0000084398", "LF0000085918", "LF0000084332", "LF0000083724", "LF0000084182", "LF0000084189", "LF0000085895", "LF0000084546", "LF0000086093", "LF0000084692", "LF0000085099", "LF0000086169", "LF0000086444", "LF0000084612", "LF0000085270", "LF0000085593", "LF0000085031", "LF0000087338", "LF0000086659", "LF0000085380", "LF0000089119", "LF0000086216", "LF0000088064", "LF0000084721", "LF0000084731", "LF0000085081", "LF0000085421", "LF0000084836", "LF0000084847", "LF0000085623", "LF0000085036", "LF0000085606", "LF0000089787", "LF0000085933", "LF0000085657", "LF0000086540", "LF0000087926", "LF0000085709", "LF0000086091", "LF0000085668", "LF0000089914", "LF0000086201", "LF0000085719", "LF0000087853", "LF0000086906", "LF0000085728", "LF0000089754", "LF0000086073", "LF0000085751", "LF0000086658", "LF0000086692", "LF0000085756", "LF0000086038", "LF0000086753", "LF0000086701", "LF0000086964", "LF0000087008", "LF0000087905", "LF0000086730", "LF0000086507", "LF0000089609", "LF0000087433", "LF0000087176", "LF0000086717", "LF0000086442", "LF0000086469", "LF0000088065", "LF0000086465", "LF0000086805", "LF0000086826", "LF0000086709", "LF0000086488", "LF0000087981", "LF0000086953", "LF0000087802", "LF0000091704", "LF0000088282", "LF0000087835", "LF0000087677", "LF0000087499", "LF0000087495", "LF0000087567", "LF0000087248", "LF0000087775", "LF0000087292", "LF0000087178", "LF0000087680", "LF0000087516", "LF0000087397", "LF0000089485", "LF0000087765", "LF0000087879", "LF0000087808", "LF0000087269", "LF0000087962", "LF0000087347", "LF0000087555", "LF0000088000", "LF0000088149", "LF0000088133", "LF0000088275", "LF0000088350", "LF0000088383", "LF0000087912", "LF0000088191", "LF0000088010", "LF0000087914", "LF0000088786", "LF0000087933", "LF0000088380", "LF0000088056", "LF0000088100", "LF0000088123", "LF0000092166", "LF0000088344", "LF0000087898", "LF0000088202", "LF0000089318", "LF0000088001", "LF0000087889", "LF0000088361", "LF0000088320", "LF0000088826", "LF0000091635", "LF0000089270", "LF0000089976", "LF0000088887", "LF0000088784", "LF0000089366", "LF0000089716", "LF0000090098", "LF0000090117", "LF0000089975", "LF0000091681", "LF0000089728", "LF0000089780", "LF0000089656", "LF0000089597", "LF0000089797", "LF0000090658", "LF0000089867", "LF0000089537", "LF0000089651", "LF0000089437", "LF0000092464", "LF0000089381", "LF0000089850", "LF0000089504", "LF0000091673", "LF0000089682", "LF0000089384", "LF0000089529", "LF0000089580", "LF0000089583", "LF0000090902", "LF0000091082", "LF0000090421", "LF0000090440", "LF0000090422", "LF0000090426", "LF0000090434", "LF0000090425", "LF0000092441", "LF0000091639", "LF0000091682", "LF0000091396", "LF0000090499", "LF0000091584", "LF0000091077", "LF0000091513", "LF0000091281", "LF0000092466", "LF0000090691", "LF0000091652", "LF0000090920", "LF0000090619", "LF0000091441", "LF0000093101", "LF0000091734", "LF0000091481", "LF0000091486", "LF0000092442", "LF0000091670", "LF0000091842", "LF0000091761", "LF0000091911", "LF0000091648", "LF0000092172", "LF0000092874", "LF0000091752", "LF0000091907", "LF0000091658", "LF0000091644", "LF0000091853", "LF0000091773", "LF0000091570", "LF0000092443", "LF0000092328", "LF0000092050", "LF0000092470", "LF0000092891", "LF0000093400", "LF0000093404", "LF0000093406", "LF0000093290", "LF0000092879", "LF0000092329", "LF0000092435", "LF0000092472", "LF0000092205", "LF0000093773", "LF0000092593", "LF0000092485", "LF0000092554", "LF0000093892", "LF0000093889", "LF0000092598", "LF0000093035", "LF0000092542", "LF0000092694", "LF0000092584", "LF0000092495", "LF0000092592", "LF0000092678", "LF0000092614", "LF0000092653", "LF0000093904", "LF0000093314", "LF0000093276", "LF0000094546", "LF0000092856", "LF0000093123", "LF0000093475", "LF0000092908", "LF0000093063", "LF0000094333", "LF0000093122", "LF0000092792", "LF0000093152", "LF0000095109", "LF0000093520", "LF0000093210", "LF0000093476", "LF0000093936", "LF0000093947", "LF0000093937", "LF0000093317", "LF0000093378", "LF0000093204", "LF0000093443", "LF0000096004", "LF0000093626", "LF0000093849", "LF0000093711", "LF0000095771", "LF0000093973", "LF0000094069", "LF0000094135", "LF0000094248", "LF0000094191", "LF0000094402", "LF0000096111", "LF0000094336", "LF0000094316", "LF0000094602", "LF0000094632", "LF0000094763", "LF0000094533", "LF0000094579", "LF0000095808", "LF0000095105", "LF0000095469", "LF0000095026", "LF0000094979", "LF0000095173", "LF0000096610", "LF0000097388", "LF0000095152", "LF0000095153", "LF0000095008", "LF0000098009", "LF0000095289", "LF0000095395", "LF0000098772", "LF0000095402", "LF0000095241", "LF0000097747", "LF0000097002", "LF0000095362", "LF0000095409", "LF0000097597", "LF0000097493", "LF0000099508", "LF0000096018", "LF0000095588", "LF0000099348", "LF0000099261", "LF0000095961", "LF0000098418", "LF0000095555", "LF0000099093", "LF0000095927", "LF0000095875", "LF0000097804", "LF0000096222", "LF0000096231", "LF0000096509", "LF0000096490", "LF0000096215", "LF0000099226", "LF0000096472", "LF0000096805", "LF0000097727", "LF0000100092", "LF0000097746", "LF0000096967", "LF0000097056", "LF0000099557", "LF0000097049", "LF0000097092", "LF0000096573", "LF0000097175", "LF0000096981", "LF0000096992", "LF0000096542", "LF0000101227", "LF0000098018", "LF0000098034", "LF0000101653", "LF0000097723", "LF0000097320", "LF0000097451", "LF0000097826", "LF0000101555", "LF0000099778", "LF0000097770", "LF0000097954", "LF0000099297", "LF0000098884", "LF0000098727", "LF0000098699", "LF0000098585", "LF0000098576", "LF0000098473", "LF0000098839", "LF0000099048", "LF0000099483", "LF0000101720", "LF0000099334", "LF0000099488", "LF0000099101", "LF0000099071", "LF0000099098", "LF0000099741", "LF0000099490", "LF0000098962", "LF0000099107", "LF0000099234", "LF0000099108", "LF0000101686", "LF0000101471", "LF0000101476", "LF0000099408", "LF0000099063", "LF0000099806", "LF0000101655", "LF0000099608", "LF0000099860", "LF0000099747", "LF0000099858", "LF0000099902", "LF0000100381", "LF0000100578", "LF0000100043", "LF0000100020", "LF0000100123", "LF0000100191", "LF0000101866", "LF0000100278", "LF0000100339", "LF0000100351", "LF0000100608", "LF0000100821", "LF0000100865", "LF0000100840", "LF0000101658", "LF0000101092", "LF0000101144", "LF0000101279", "LF0000101377", "LF0000101052", "LF0000101252", "LF0000102119", "LF0000101787", "LF0000101833", "LF0000101412", "LF0000100910", "LF0000100825", "LF0000101334", "LF0000101800", "LF0000083739", "LF0000083762", "LF0000085378", "LF0000085379", "LF0000084017", "LF0000084018", "LF0000083976", "LF0000083561", "LF0000083562", "LF0000083564", "LF0000083565", "LF0000083759", "LF0000084187", "LF0000083935", "LF0000083938", "LF0000084035", "LF0000084076", "LF0000084113", "LF0000084121", "LF0000083878", "LF0000083904", "LF0000083880", "LF0000084179", "LF0000083864", "LF0000083879", "LF0000084585", "LF0000085275", "LF0000085210", "LF0000085212", "LF0000085192", "LF0000085194", "LF0000084998", "LF0000085000", "LF0000085598", "LF0000085604", "LF0000085627", "LF0000086413", "LF0000084656", "LF0000084658", "LF0000086096", "LF0000086097", "LF0000086056", "LF0000086057", "LF0000087429", "LF0000087432", "LF0000085716", "LF0000085842", "LF0000088039", "LF0000088042", "LF0000088038", "LF0000088040", "LF0000086673", "LF0000086680", "LF0000086448", "LF0000086460", "LF0000087676", "LF0000087683", "LF0000087644", "LF0000087656", "LF0000087241", "LF0000087643", "LF0000087350", "LF0000087368", "LF0000087568", "LF0000087586", "LF0000087403", "LF0000087414", "LF0000087800", "LF0000087807", "LF0000088260", "LF0000088413", "LF0000088060", "LF0000088061", "LF0000091671", "LF0000091674", "LF0000089179", "LF0000089980", "LF0000092120", "LF0000092121", "LF0000088876", "LF0000089177", "LF0000089265", "LF0000089266", "LF0000088839", "LF0000088840", "LF0000089491", "LF0000089492", "LF0000092400", "LF0000092401", "LF0000089022", "LF0000089023", "LF0000090095", "LF0000090110", "LF0000090044", "LF0000090047", "LF0000092330", "LF0000092547", "LF0000090004", "LF0000090005", "LF0000089470", "LF0000089695", "LF0000089568", "LF0000089826", "LF0000091760", "LF0000091762", "LF0000089750", "LF0000089757", "LF0000089613", "LF0000089615", "LF0000089960", "LF0000089961", "LF0000089675", "LF0000089677", "LF0000089587", "LF0000089588", "LF0000089506", "LF0000089507", "LF0000090420", "LF0000091084", "LF0000090478", "LF0000090479", "LF0000090473", "LF0000091093", "LF0000090490", "LF0000091229", "LF0000090610", "LF0000090612", "LF0000091434", "LF0000092773", "LF0000091949", "LF0000091954", "LF0000091499", "LF0000091898", "LF0000092327", "LF0000092550", "LF0000092427", "LF0000092711", "LF0000091625", "LF0000091834", "LF0000091529", "LF0000091920", "LF0000091893", "LF0000091894", "LF0000092886", "LF0000092887", "LF0000091771", "LF0000091772", "LF0000091740", "LF0000091741", "LF0000093032", "LF0000093033", "LF0000092203", "LF0000092204", "LF0000093471", "LF0000093472", "LF0000092407", "LF0000092459", "LF0000092706", "LF0000093473", "LF0000092456", "LF0000092458", "LF0000092483", "LF0000092484", "LF0000092630", "LF0000092657", "LF0000094423", "LF0000094425", "LF0000093908", "LF0000093909", "LF0000092718", "LF0000092719", "LF0000092767", "LF0000092768", "LF0000093078", "LF0000093079", "LF0000093363", "LF0000093364", "LF0000093939", "LF0000093940", "LF0000093955", "LF0000093956", "LF0000093640", "LF0000093641", "LF0000093952", "LF0000093958", "LF0000093717", "LF0000093718", "LF0000094270", "LF0000094271", "LF0000093577", "LF0000093579", "LF0000096526", "LF0000096527", "LF0000095014", "LF0000095016", "LF0000094969", "LF0000094970", "LF0000097466", "LF0000097467", "LF0000096205", "LF0000096206", "LF0000096284", "LF0000096285", "LF0000096878", "LF0000096880", "LF0000096539", "LF0000096541", "LF0000097020", "LF0000097021", "LF0000096995", "LF0000096996", "LF0000097007", "LF0000097008", "LF0000098587", "LF0000098923", "LF0000096989", "LF0000096991", "LF0000097023", "LF0000097024", "LF0000096984", "LF0000096985", "LF0000097026", "LF0000097027", "LF0000096979", "LF0000096980", "LF0000098730", "LF0000100478", "LF0000099539", "LF0000099541", "LF0000099452", "LF0000099457", "LF0000099971", "LF0000099972", "LF0000100360", "LF0000100361", "LF0000101226", "LF0000101229", "LF0000100948", "LF0000100949", "LF0000100848", "LF0000100849", "LF0000084007", "LF0000084023", "LF0000084445", "LF0000084446", "LF0000083700", "LF0000084251", "LF0000084275", "LF0000085934", "LF0000085939", "LF0000085942", "LF0000083747", "LF0000084184", "LF0000084186", "LF0000083912", "LF0000083919", "LF0000083921", "LF0000083792", "LF0000083815", "LF0000083837", "LF0000084020", "LF0000084038", "LF0000083974", "LF0000085047", "LF0000085096", "LF0000085670", "LF0000084755", "LF0000084773", "LF0000084786", "LF0000085721", "LF0000086068", "LF0000086070", "LF0000086066", "LF0000086168", "LF0000086200", "LF0000085962", "LF0000085963", "LF0000085968", "LF0000086678", "LF0000086681", "LF0000087262", "LF0000086930", "LF0000086932", "LF0000086933", "LF0000087813", "LF0000087818", "LF0000087819", "LF0000090528", "LF0000090530", "LF0000090531", "LF0000088749", "LF0000088750", "LF0000088752", "LF0000088584", "LF0000089015", "LF0000089020", "LF0000089021", "LF0000089843", "LF0000089845", "LF0000089847", "LF0000090053", "LF0000090093", "LF0000090204", "LF0000089581", "LF0000089586", "LF0000089590", "LF0000090737", "LF0000090738", "LF0000092437", "LF0000089839", "LF0000089846", "LF0000089848", "LF0000089489", "LF0000089493", "LF0000089494", "LF0000090949", "LF0000090951", "LF0000091306", "LF0000090725", "LF0000090727", "LF0000090730", "LF0000090548", "LF0000090549", "LF0000090550", "LF0000090851", "LF0000090852", "LF0000091436", "LF0000091438", "LF0000091439", "LF0000091440", "LF0000090579", "LF0000090580", "LF0000090581", "LF0000091480", "LF0000091491", "LF0000091497", "LF0000091905", "LF0000091906", "LF0000091908", "LF0000093028", "LF0000093029", "LF0000093030", "LF0000093878", "LF0000093880", "LF0000093883", "LF0000093879", "LF0000093881", "LF0000093884", "LF0000093896", "LF0000093898", "LF0000093899", "LF0000095047", "LF0000095052", "LF0000095053", "LF0000093811", "LF0000093813", "LF0000093835", "LF0000094098", "LF0000094099", "LF0000094106", "LF0000093961", "LF0000093962", "LF0000093963", "LF0000097096", "LF0000097098", "LF0000097101", "LF0000095191", "LF0000095192", "LF0000095193", "LF0000097111", "LF0000097112", "LF0000097113", "LF0000095777", "LF0000095780", "LF0000095781", "LF0000096118", "LF0000096120", "LF0000096121", "LF0000097122", "LF0000097123", "LF0000097124", "LF0000096361", "LF0000096363", "LF0000096365", "LF0000096306", "LF0000096307", "LF0000096308", "LF0000099899", "LF0000099900", "LF0000099901", "LF0000097126", "LF0000097128", "LF0000097129", "LF0000096998", "LF0000097000", "LF0000097001", "LF0000098264", "LF0000098265", "LF0000098267", "LF0000097327", "LF0000097903", "LF0000097905", "LF0000083538", "LF0000083861", "LF0000083877", "LF0000083609", "LF0000083613", "LF0000083624", "LF0000083627", "LF0000084321", "LF0000084323", "LF0000084325", "LF0000084338", "LF0000083869", "LF0000083873", "LF0000084180", "LF0000083790", "LF0000083839", "LF0000083851", "LF0000083875", "LF0000083947", "LF0000085063", "LF0000085064", "LF0000085066", "LF0000085070", "LF0000084587", "LF0000084627", "LF0000084707", "LF0000085157", "LF0000084564", "LF0000085013", "LF0000085079", "LF0000085311", "LF0000087025", "LF0000087026", "LF0000087027", "LF0000087028", "LF0000086921", "LF0000087101", "LF0000087105", "LF0000087134", "LF0000087221", "LF0000087493", "LF0000087652", "LF0000087868", "LF0000087796", "LF0000087797", "LF0000087801", "LF0000087817", "LF0000088137", "LF0000088139", "LF0000088140", "LF0000088143", "LF0000092115", "LF0000092116", "LF0000092117", "LF0000092118", "LF0000090541", "LF0000090543", "LF0000090544", "LF0000090546", "LF0000089751", "LF0000089752", "LF0000089753", "LF0000089756", "LF0000089638", "LF0000089640", "LF0000089641", "LF0000089642", "LF0000090774", "LF0000090778", "LF0000090780", "LF0000090787", "LF0000091552", "LF0000091553", "LF0000091788", "LF0000091789", "LF0000092043", "LF0000092045", "LF0000092046", "LF0000092047", "LF0000092897", "LF0000092898", "LF0000092899", "LF0000092900", "LF0000093038", "LF0000093039", "LF0000093040", "LF0000093042", "LF0000093914", "LF0000093916", "LF0000093917", "LF0000093924", "LF0000093918", "LF0000093919", "LF0000093926", "LF0000093927", "LF0000093921", "LF0000093922", "LF0000093923", "LF0000093925", "LF0000093044", "LF0000093046", "LF0000093047", "LF0000093048", "LF0000093175", "LF0000093176", "LF0000093270", "LF0000093271", "LF0000093931", "LF0000093932", "LF0000093933", "LF0000093934", "LF0000094977", "LF0000094978", "LF0000094980", "LF0000094981", "LF0000097104", "LF0000097105", "LF0000097106", "LF0000097107", "LF0000096613", "LF0000096614", "LF0000096885", "LF0000096886", "LF0000099340", "LF0000099342", "LF0000099344", "LF0000099345", "LF0000101710", "LF0000101711", "LF0000101714", "LF0000101716", "LF0000083720", "LF0000084253", "LF0000084274", "LF0000084305", "LF0000084306", "LF0000087296", "LF0000087297", "LF0000087300", "LF0000087359", "LF0000087363", "LF0000083844", "LF0000083847", "LF0000083848", "LF0000083895", "LF0000084519", "LF0000084593", "LF0000085816", "LF0000085818", "LF0000085322", "LF0000085323", "LF0000085324", "LF0000085325", "LF0000085326", "LF0000084754", "LF0000084765", "LF0000084769", "LF0000084782", "LF0000084798", "LF0000085429", "LF0000085430", "LF0000085431", "LF0000085433", "LF0000085434", "LF0000087308", "LF0000087309", "LF0000087310", "LF0000087311", "LF0000087319", "LF0000086885", "LF0000086886", "LF0000086888", "LF0000087017", "LF0000087033", "LF0000089076", "LF0000089078", "LF0000089079", "LF0000089080", "LF0000089081", "LF0000090057", "LF0000090064", "LF0000090074", "LF0000090257", "LF0000090258", "LF0000091245", "LF0000091433", "LF0000091448", "LF0000091452", "LF0000091461", "LF0000091124", "LF0000091125", "LF0000091126", "LF0000091127", "LF0000091852", "LF0000090601", "LF0000090602", "LF0000090603", "LF0000090604", "LF0000090605", "LF0000091536", "LF0000091537", "LF0000091538", "LF0000091539", "LF0000091540", "LF0000093894", "LF0000093900", "LF0000093905", "LF0000093906", "LF0000093907", "LF0000092954", "LF0000092957", "LF0000092958", "LF0000092960", "LF0000092962", "LF0000093296", "LF0000093297", "LF0000093298", "LF0000093300", "LF0000093301", "LF0000097115", "LF0000097116", "LF0000097118", "LF0000097119", "LF0000097120", "LF0000097485", "LF0000097487", "LF0000097488", "LF0000097489", "LF0000097490", "LF0000083571", "LF0000083610", "LF0000083611", "LF0000083612", "LF0000083625", "LF0000083626", "LF0000084375", "LF0000087302", "LF0000087303", "LF0000087304", "LF0000087305", "LF0000087306", "LF0000087371", "LF0000084556", "LF0000084557", "LF0000084558", "LF0000084559", "LF0000084560", "LF0000085089", "LF0000085663", "LF0000085929", "LF0000085958", "LF0000085959", "LF0000085979", "LF0000085980", "LF0000086814", "LF0000086818", "LF0000086819", "LF0000086820", "LF0000086821", "LF0000086823", "LF0000087041", "LF0000087046", "LF0000087047", "LF0000087048", "LF0000087049", "LF0000087050", "LF0000087579", "LF0000087787", "LF0000087788", "LF0000087865", "LF0000088031", "LF0000088032", "LF0000090533", "LF0000090534", "LF0000090535", "LF0000090537", "LF0000090538", "LF0000090545", "LF0000090366", "LF0000090367", "LF0000090368", "LF0000090369", "LF0000090372", "LF0000091915", "LF0000089462", "LF0000093941", "LF0000093942", "LF0000093943", "LF0000093944", "LF0000093946", "LF0000093950", "LF0000083907", "LF0000083908", "LF0000083910", "LF0000083914", "LF0000083915", "LF0000083918", "LF0000086207", "LF0000086213", "LF0000086225", "LF0000086232", "LF0000086260", "LF0000086267", "LF0000086271", "LF0000087318", "LF0000087320", "LF0000087321", "LF0000087323", "LF0000087324", "LF0000087325", "LF0000087327", "LF0000089571", "LF0000089572", "LF0000089574", "LF0000089575", "LF0000089577", "LF0000089591", "LF0000089592", "LF0000091001", "LF0000091003", "LF0000091005", "LF0000091008", "LF0000091010", "LF0000091011", "LF0000091013"];
        foreach($lifting_ids as $lifting_id)
        {
            $lifting_id =  ltrim($lifting_id, 'LF0');
            try 
            {
                $lifting = Lifting::find($lifting_id) ;

                if(empty($lifting)){
                        array_push($errMsg, "No lifting records found for lifting id ".$lifting_id);
                        continue;
                }
                $reward = Reward::find($lifting->reward[0]->id) ;
                
                if(empty($reward)){
                        array_push($errMsg, "No rewards records found to that lifting for lifting id ".$lifting_id);
                        continue;
                }
                DB::beginTransaction();
                    $verified_by_history = [];
                    $verified_by_at_history = [];
                    $reward = Reward::where('lifting_id', $lifting_id)->first();
                    if($reward != null )
                    {
                        if($reward->verified_by_history != null)
                        {
                            $verified_by_history = json_decode($reward->verified_by_history);
                        }
                        else
                        {
                            if($reward->verified_by != null)
                            {
                                array_push($verified_by_history, $reward->verified_by);
                            }
                        }
                    }
                    array_push($verified_by_history, Auth::user()->id);
                    if($reward != null )
                    {
                        if($reward->verified_by_at_history != null)
                        {
                            $verified_by_at_history = json_decode($reward->verified_by_at_history);
                        }
                        else
                        {
                            if($reward->verified_by_at != null)
                            {
                                array_push($verified_by_at_history, $reward->verified_by_at);
                            }
                        }
                    }
                    array_push($verified_by_at_history, Carbon::now()->format('y-m-d H:i:s'));
                    $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                    if($is_verified == Reward::VERIFIED)
                    {
                        $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                    }
                    Reward::where('lifting_id', $lifting_id)->update([
                            'is_verified' => $is_verified,
                            'verified_by_at' => Carbon::now()->format('y-m-d H:i:s'),
                            'verified_by_at_history' => json_encode($verified_by_at_history),
                            'verified_by' => Auth::user()->id,
                            'verified_by_history' => json_encode($verified_by_history),
                            'is_eligible_for_ledger' => $isEligibleForLedgerInRewardTable,
                        ]);
                    if($lifting->req_type == 2)
                    {
                        if($is_verified == 1)
                        {
                            $lifting->update([
                                'req_status' => 1,
                                // 'action_taken_at' => Carbon::now()->format('y-m-d H:i:s')
                                'action_taken_by' => \Auth::user()->id,
                            ]);
                        }
                        else if($is_verified == 2)
                        {
                            $lifting->update([
                                'req_status' => 2,
                                // 'action_taken_at' => Carbon::now()->format('y-m-d H:i:s')
                                'action_taken_by' => \Auth::user()->id,
                            ]);
                        }
                        // else
                        // {
                        //     $lifting->update([
                        //         'req_status' => 0,
                        //         'action_taken_at' => null
                        //     ]);
                        // }
                        $liftingApprovalHistory = LiftingApprovalHistory::where('lifting_id', $lifting->id)->orderBy('id', 'DESC')->first();
                        $liftingApprovalHistoryActionStatus = null;
                        if(in_array($is_verified, [0, 1]) && $liftingApprovalHistory != null && $liftingApprovalHistory->action_status != 3)
                        {
                            $liftingApprovalHistoryActionStatus = 3;
                        }
                        else if($is_verified == 2 && $liftingApprovalHistory != null && $liftingApprovalHistory->action_status != 4)
                        {
                            $liftingApprovalHistoryActionStatus = 4;
                        }

                        if($liftingApprovalHistoryActionStatus != null)
                        {
                            $point = 0;
                            $bonusPoint = 0;
                            $rewards = Reward::where('lifting_id', $lifting->id)->get();
                            $teName = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->with('mason')->first();
                            $approvalWindowSettingName = $liftingApprovalHistory->seek_approval == 1 ? 'dealer/rssd_approval_window' : 'bdo_approval_window';
                            foreach($rewards as $val)
                            {
                                if($val->is_bonus == 0){ 
                                    $point = $val->point; 
                                } 
                                else{ 
                                    $bonusPoint = $val->point;
                                }
                            }
                            $liftingApprovalHistory = [
                                'lifting_id' => $lifting->id,
                                'qty' => $lifting->qty,
                                'point' => $point,
                                'bonus_point' => $bonusPoint,
                                'seek_approval' => $liftingApprovalHistory->seek_approval,
                                'seek_approval_by' => $teName->mason->parent ?? 0,
                                'seek_approval_from' => $lifting->seek_approval_from,
                                'approval_window' => $this->settingVal('setting_name', $approvalWindowSettingName),
                                'action_status' => $liftingApprovalHistoryActionStatus,
                                'action_taken_by' => \Auth::user()->id,
                            ];
                            LiftingApprovalHistory::create($liftingApprovalHistory);
                        }
                    }
                
                // Update total user point.
                $this->updatePoint($lifting->reward[0]->user_id);
                DB::commit();
                
            }
            catch (Exception $e) 
            {
                
                DB::rollBack();
                array_push($errMsg, "error for lifting id ".$lifting_id." error msg -> ".$e->getMessage());
                continue;
                
            }
        }
        return "update successfully.";
    }

    public static function getLifting90($product_id='', $dealer_id='')
    {
        // find the 3 month before month and years
        $curr = '07-2023';
        $month1 = '05-2023';
        $month2 = '04-2023';
    
         $arr1 = explode("-",$month1);
         $arr2 = explode("-",$month2);
    
         $years=array($arr1[1],$arr2[1]);
        $marr1 = ltrim($arr1[0],'0');
        $marr2 = ltrim($arr2[0],'0');    
         $months=array($marr1,$marr2);
  
  
         
        // find the 3 month before month and years
        // $liftcount  = Lifting::where('user_id', $dealer_id)->count();
        //   $datas = CustomerLifting::whereIn('year', $years)->whereIn('month', $months)->where('dealer_id', $dealer_id)->where('product_id', $product_id)->sum('quantity');  
        $datas1 = CustomerLifting::where('year', $years[0])->where('month', $months[0])
        ->where('dealer_id', $dealer_id)
        ->where('product_id', $product_id)->sum('quantity');  
        
        $datas2 = CustomerLifting::where('year', $years[1])->where('month', $months[1])
        ->where('dealer_id', $dealer_id)
        ->where('product_id', $product_id)->sum('quantity');  
        
        $datas = $datas1+$datas2;
        //dd($datas);
        if($datas){           
            $avglifts = $datas/2;           
            // $res = ($avglifts*90)/100;
            $res = $avglifts;
           // dd($res);
            return $res;
        }        
        return null ;
    }
    public static function getCurrentMonthLifting($product_id='', $dealer_id='')
    {
     // find the current month lifting of masson
       // Get the first day of the current month
        $firstDayOfMonth = '2023-07-01';

        // Get the last day of the current month
        $lastDayOfMonth = '2023-07-31';
        
           $liftIdArr = DB::table('lifting')
             ->where('user_id', $dealer_id)
            // ->whereBetween('lifting_date', [$firstDayOfMonth, $lastDayOfMonth])
           // ->whereRaw("DATE_FORMAT(lifting_date, '%Y-%m-%d') between '{$firstDayOfMonth}' and '{$lastDayOfMonth}'")          
           ->whereRaw("STR_TO_DATE(lifting_date, '%d-%m-%Y') between '{$firstDayOfMonth}' and '{$lastDayOfMonth}'")          
           ->where('product_id', $product_id)
            ->pluck('id')
            ->toArray();
        // dd($liftIdArr);
                $datas = Reward::whereIn('lifting_id', $liftIdArr)
                        ->where('is_verified', 1)
                        ->where('is_bonus', 0)
                        ->sum('bag');  
        //  dd($datas);
       if($datas){   
            return $datas;
        }        
        return 0 ;
    }
    // END correcting error unverified to verified
    
    public function showBulkLiftingUpdateForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('lifting.bulk-update') ;
        if($request->session()->exists('lifting_bulk_update')){
            return view('admin.lifting.progress') ;
        }

        return view('admin.lifting.bulk-upload') ;

    }

    public function saveBulkLiftingUpdateForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('lifting.bulk-update') ;
        set_time_limit(0);
        try {

            if($request->hasFile('csvFile'))
            {
                $file       = $request->file('csvFile');
                $folderPath = \Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  \Storage::disk('public')->path($folderPath) ;
                $records = array_map('str_getcsv', file($fileWithPath));
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = 0; $unProcessedCount = 0;
                session()->put('lifting_bulk_update', $fileWithPath) ;
                session()->put('lifting_bulk_update_count', 0) ;
                $unprocessedData=[];
                $i=1;
                $importingFile = fopen($fileWithPath, 'r');
                // $headers = fgetcsv($importingFile);
                $row = [];
                $rowCount = 0;
                $verified_status_name_arr = ["unverified", "verified", "rejected"];
                while (($rowLine = fgetcsv($importingFile)) !== false) 
                {
                    try
                    {
                        DB::beginTransaction();
                            if($rowCount > 0){
                                $row = array_map(function ($value) {
                                    return str_replace(["\r", "\n"], ' ', $value);
                                }, $rowLine);

                                // $unprocessedData[] = gettype($row)."...".count($row);
                                $lifting_id_decoration = $row[0];
                                $is_verified_status_valid = array_key_exists($row[1], $verified_status_name_arr);
                                $lifting_id =  ltrim($lifting_id_decoration, 'LF0');
                                $lifting = Lifting::lockForUpdate()->find($lifting_id) ;

                                if(empty($lifting)){
                                        array_push($unprocessedData, "<br>No lifting records found for lifting id ".$lifting_id_decoration.", at row ".$rowCount);
                                        $i++;
                                        $rowCount++;
                                        $unProcessedCount++;
                                        DB::rollBack();
                                        continue;
                                }
                                $reward = Reward::lockForUpdate()->find($lifting->reward[0]->id);

                                if(empty($reward)){
                                        array_push($unprocessedData, "<br>No rewards records found to that lifting for lifting id ".$lifting_id_decoration.", at row ".$rowCount);
                                        $i++;
                                        $rowCount++;
                                        $unProcessedCount++;
                                        DB::rollBack();
                                        continue;
                                }
                                if(!$is_verified_status_valid){
                                        array_push($unprocessedData, "<br>Inalid verified status for lifting id ".$lifting_id_decoration.", at row ".$rowCount);
                                        $i++;
                                        $rowCount++;
                                        $unProcessedCount++;
                                        DB::rollBack();
                                        continue;
                                }
                                $verified_status = $row[1];
                                if($reward->is_verified == $verified_status){
                                    $verified_status_name = $verified_status_name_arr[$verified_status];
                                    
                                    array_push($unprocessedData, "<br>Status already ".$verified_status_name." for lifting id ".$lifting_id_decoration.", at row ".$rowCount);
                                    $i++;
                                    $rowCount++;
                                    $unProcessedCount++;
                                    DB::rollBack();
                                    continue;
                                }

                                if($reward->is_verified == Reward::VERIFIED)
                                {
                                    $getMasonRedeptions = UserCatalogueRedeemtion::where("user_id", $reward->user_id)
                                        ->where("status", "!=", UserCatalogueRedeemtion::STATUS_REJECTED)
                                        ->where("status", "!=", UserCatalogueRedeemtion::STATUS_UNDELIVERED)
                                        ->lockForUpdate()
                                        ->get();

                                    $getMasonRedeemPoint = $getMasonRedeptions->sum("redeemed_point");

                                    // $getSumOfPointUptoUpdatingLiftingReward = Reward::where("user_id", $reward->user_id)->where("is_verified", Reward::VERIFIED)->where('id', "<=", );
                                    $getSumOfPointUptoUpdatingLiftingReward = DB::table('lifting')
                                    ->rightJoin('rewards', 'rewards.lifting_id', '=', 'lifting.id')
                                    ->where('rewards.user_id', $reward->user_id)
                                    ->where(function($q) use ($lifting_id) {
                                        $q->where('lifting.id', '<=', $lifting_id)
                                        ->orWhereNull('rewards.lifting_id');
                                    })
                                    ->where('rewards.is_verified', Reward::VERIFIED)
                                    ->sum('rewards.point');

                                    $liftingTotalPoint = $lifting->reward->sum("point");
                                   
                                    if(($getSumOfPointUptoUpdatingLiftingReward - $liftingTotalPoint) < $getMasonRedeemPoint)
                                    {
                                        array_push($unprocessedData, "<br>Lifting ".$lifting_id_decoration." cannot be update as it has been used for redeemtion, at row ".$rowCount);
                                        $i++;
                                        $rowCount++;
                                        $unProcessedCount++;
                                        DB::rollBack();
                                        continue;
                                    }
                                }
                                
                                $verified_by_history = [];
                                $verified_by_at_history = [];
                                
                               
                                if($reward->verified_by_history != null)
                                {
                                    $verified_by_history = json_decode($reward->verified_by_history);
                                }
                                else
                                {
                                    if($reward->verified_by != null)
                                    {
                                        array_push($verified_by_history, $reward->verified_by);
                                    }
                                }
                                array_push($verified_by_history, 0);
                                if($reward->verified_by_at_history != null)
                                {
                                    $verified_by_at_history = json_decode($reward->verified_by_at_history);
                                }
                                else
                                {
                                    if($reward->verified_by_at != null)
                                    {
                                        array_push($verified_by_at_history, $reward->verified_by_at);
                                    }
                                }
                                array_push($verified_by_at_history, Carbon::now()->format('y-m-d H:i:s'));
                                //keeping Reward History
                                $rewardRecords = Reward::where('lifting_id', $lifting_id)->get();
                                foreach($rewardRecords as $rewardRecord)
                                {
                                    $isEligibleForLedger = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                                    if(RewardHistory::where("lifting_id", $lifting_id)->where("is_verified", Reward::VERIFIED)->count() < 1)
                                    {
                                        if($rewardRecord->is_verified == Reward::VERIFIED)
                                        {
                                            $isEligibleForLedger = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                                        }
                                    }
                                    else
                                    {
                                        $lastRewardHistoryRec = RewardHistory::where("reward_id", $rewardRecord->id)->latest("id")->first();

                                        if(!empty($lastRewardHistoryRec)){

                                            if($lastRewardHistoryRec->is_verified == Reward::VERIFIED)
                                            {
                                                if($rewardRecord->is_verified != Reward::VERIFIED)
                                                {
                                                    $isEligibleForLedger = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                                                }
                                            }
                                            else
                                            {
                                                if($rewardRecord->is_verified == Reward::VERIFIED)
                                                {
                                                    $isEligibleForLedger = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                                                }
                                            }
                                        }
                                      
                                    }

                                    RewardHistory::create([
                                        'reward_id' => $rewardRecord->id,
                                        'point' => $rewardRecord->point,
                                        'bag' => $rewardRecord->bag,
                                        'lifting_id' => $rewardRecord->lifting_id,
                                        'user_id' => $rewardRecord->user_id,
                                        'date' => $rewardRecord->date,
                                        'is_verified' => $rewardRecord->is_verified,
                                        'verified_by' => $rewardRecord->verified_by,
                                        'verified_by_at' => $rewardRecord->verified_by_at,
                                        'is_bonus' => $rewardRecord->is_bonus,
                                        'description' => $rewardRecord->description,
                                        'show_point' => $rewardRecord->show_point,
                                        'is_eligible_for_ledger' => $isEligibleForLedger,
                                        'reward_date_time' => $rewardRecord->updated_at,
                                        'attachment' => $rewardRecord->attachment,
                                        'remarks' => $rewardRecord->remarks,
                                    ]);
                                }
                                $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                                if($reward->is_verified == Reward::VERIFIED)
                                {
                                    if($row[1] != Reward::VERIFIED)
                                    {
                                        $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                                    }
                                }
                                else
                                {
                                    if($row[1] == Reward::VERIFIED)
                                    {
                                        $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                                    }
                                }
                                Reward::where('lifting_id', $lifting_id)->update([
                                    'is_verified' => $verified_status,
                                    'verified_by_at' => Carbon::now()->format('y-m-d H:i:s'),
                                    'verified_by_at_history' => json_encode($verified_by_at_history),
                                    'verified_by' => 0,
                                    'verified_by_history' => json_encode($verified_by_history),
                                    'is_eligible_for_ledger' => $isEligibleForLedgerInRewardTable,
                                ]);
                                if($lifting->req_type == 2)
                                {
                                    if(($verified_status == 1)  || ($verified_status == 0))
                                    {
                                        $lifting->update([
                                            'req_status' => 1,
                                            'action_taken_at' => Carbon::now()->format('y-m-d H:i:s'),
                                            'action_taken_by' => \Auth::user()->id,
                                        ]);
                                    }
                                    else if($verified_status == 2)
                                    {
                                        $lifting->update([
                                            'req_status' => 2,
                                            'action_taken_at' => Carbon::now()->format('y-m-d H:i:s'),
                                            'action_taken_by' => \Auth::user()->id,
                                        ]);
                                    }
                                    $liftingApprovalHistory = LiftingApprovalHistory::where('lifting_id', $lifting->id)->orderBy('id', 'DESC')->first();
                                    $liftingApprovalHistoryActionStatus = null;
                                    if(in_array($verified_status, [0, 1]) && $liftingApprovalHistory != null && $liftingApprovalHistory->action_status != 3)
                                    {
                                        $liftingApprovalHistoryActionStatus = 3;
                                    }
                                    else if($verified_status == 2 && $liftingApprovalHistory != null && $liftingApprovalHistory->action_status != 4)
                                    {
                                        $liftingApprovalHistoryActionStatus = 4;
                                    }

                                    if($liftingApprovalHistoryActionStatus != null)
                                    {
                                        $point = 0;
                                        $bonusPoint = 0;
                                        // $rewards = Reward::where('lifting_id', $lifting->id)->with('mason')->get();
                                        $rewards = $lifting->reward;
                                        $teName = $rewards[0];
                                        $approvalWindowSettingName = $liftingApprovalHistory->seek_approval == 1 ? 'dealer/rssd_approval_window' : 'bdo_approval_window';
                                        foreach($rewards as $val)
                                        {
                                            if($val->is_bonus == 0){
                                                $point = $val->point;
                                            }
                                            else{
                                                $bonusPoint = $val->point;
                                            }
                                        }
                                        $liftingApprovalHistory = [
                                            'lifting_id' => $lifting->id,
                                            'qty' => $lifting->qty,
                                            'point' => $point,
                                            'bonus_point' => $bonusPoint,
                                            'seek_approval' => $liftingApprovalHistory->seek_approval,
                                            'seek_approval_by' => $teName->mason->parent ?? 0,
                                            'seek_approval_from' => $lifting->seek_approval_from,
                                            'approval_window' => $this->settingVal('setting_name', $approvalWindowSettingName),
                                            'action_status' => $liftingApprovalHistoryActionStatus,
                                            'action_taken_by' => \Auth::user()->id,
                                        ];
                                        LiftingApprovalHistory::create($liftingApprovalHistory);
                                    }
                                }


                                // Update total user point.
                                $this->updatePoint($lifting->reward[0]->user_id);
                                
                                
                            }
                        DB::commit();
                        $i++;
                        $rowCount++;
                    }
                    catch (\Exception $e)
                    {

                        DB::rollBack();
                        array_push($unprocessedData, "<br>error for lifting id ".$lifting_id_decoration." error msg -> ".$e->getMessage().", at row ".$rowCount);
                        $i++;
                        $rowCount++;
                        $unProcessedCount++;
                        continue;

                    }
                    $count++;
                    session()->put('lifting_bulk_update_count', $count) ;
                }
                fclose($importingFile);
                $request->session()->forget('lifting_bulk_update');
                $liftingRecordsCount = session()->get('lifting_bulk_update_count');
                $request->session()->forget('lifting_bulk_update_count');
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Upload Successfully '.$rowCount.' records, '.$liftingRecordsCount.' records processed. & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200);

            }
            else 
            {
                $request->session()->forget('lifting_bulk_update');
                $request->session()->forget('lifting_bulk_update_count');
                return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Input csvFile is required.'], 200); ;
            }
        } catch (\Exception $e) {
            $request->session()->forget('lifting_bulk_update');
            $request->session()->forget('lifting_bulk_update_count');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200);
        }

    }
}
