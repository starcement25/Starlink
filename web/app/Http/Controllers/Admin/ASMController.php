<?php

namespace App\Http\Controllers\Admin;

use App\Models\LiftingApprovalHistory;
use Flash;
use App\Models\User;
use App\Models\Lifting;
use App\Utils\Helper;
use App\Models\Branch;
use App\Models\Log;
use App\Models\Reward;
use App\Models\RewardHistory;
use Exception;
use Carbon\Carbon;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\DataTables\ASMDataTable;
use App\Repositories\ASMRepository;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\ASM\CreateASMRequest;
use App\Http\Requests\ASM\UpdateASMRequest;

class ASMController extends AppBaseController
{
    use HelperTrait;
    /** @var ASMRepository $asmRepository*/
    private $asmRepository;

    public function __construct(ASMRepository $asmRepo)
    {
        $this->asmRepository = $asmRepo;
    }

    /**
     * Display a listing of the ASm.
     */
    public function index(ASMDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.view') ;
       return $dataTable->render('admin.asm.index') ;

    }

    /**
     * Show the form for creating a new ASM.
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.create') ;
        $branchOptions = Branch::where('asm_user_id', 0)->orWhere('asm_user_id', null)->pluck('name', 'id')->toArray();
        // return $branchOptions = array_merge(["" => "Select Branch"], $branchOptions);
        $branchSelected = "";
        return view('admin.asm.create')->with([
            "branchOptions" => $branchOptions,
            "branchSelected" => $branchSelected,
        ]);
    }

    /**
     * Store a newly created ASM in storage.
     */
    public function store(CreateASMRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.create') ;
        try{
            if(empty($request->branch_ids[0] ?? "")) {
                return redirect(route('asm.create'))->withErrors(["branch_ids" => "Branch Required"])->withInput();
            }
            $input = $request->except("branch_ids");
            $input['role'] = $this->getReservedRoleId('ASM');
            \DB::beginTransaction();
                $asm = User::create($input);
                foreach($request->branch_ids as $branchId)
                {
                    $branch = Branch::find($branchId);
                    if($branch->asm_user_id != 0)
                    {
                        continue;
                    }
                    $branch->update([
                        'asm_user_id' => $asm->id,
                    ]);
                }
            \DB::commit();
            Flash::success('ASM has been created successfully.');
            return redirect(route('asm.index'));
        }
        catch (Exception $e)
        {
            \DB::rollBack();
            Flash::error($e->getMessage());

            return redirect(route('asm.index'));
        }
    }

    /**
     * Display the specified ASM.
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.view') ;
        $asm = User::find($id);

        if (empty($asm)) {
            Flash::error('ASM not found');

            return redirect(route('asm.index'));
        }

        return view('admin.asm.show')->with('asm', $asm);
    }

    /**
     * Show the form for editing the specified ASM.
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.edit') ;
        $asm = User::find($id);

        if (empty($asm)) {
            Flash::error('ASM not found');

            return redirect(route('asm.index'));
        }
        $branchOptions = Branch::where('asm_user_id', 0)->orWhere('asm_user_id', null)->orWhere('asm_user_id', $asm->id)->pluck('name', 'id')->toArray();
        // $branchOptions = array_merge(["" => "Select Branch"], $branchOptions);
        $branchSelected = Branch::where('asm_user_id', $asm->id)->pluck('id')->toArray();
        return view('admin.asm.edit')->with([
            'asm' => $asm,
            'branchOptions' => $branchOptions,
            'branchSelected' => $branchSelected,
        ]);
    }

    /**
     * Update the specified ASm in storage.
     */
    public function update($id, UpdateASMRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.edit') ;
        try{
            $asm = User::find($id);

            if (empty($asm)) {
                Flash::error('ASM not found');

                return redirect(route('asm.index'));
            }
            // if (empty($request->branch_ids[0] ?? "")) {
            //     return redirect(route('asm.edit', ['asm' => $id]))->withErrors(["branch_ids" => "Branch Required"])->withInput();
            // }
            \DB::beginTransaction();
                $input = $request->except('branch_ids') ;
                $asm->update($input);
                
                Branch::where('asm_user_id', $asm->id)->update([
                    'asm_user_id' => 0,
                ]);
                    
                foreach(($request->branch_ids ?? []) as $branchId)
                {
                    $branch = Branch::find($branchId);
                    if($branch->asm_user_id != 0)
                    {
                        continue;
                    }
                    $branch->update([
                        'asm_user_id' => $asm->id,
                    ]);
                }
            \DB::commit();

            Flash::success('ASM updated successfully.');

            return redirect(route('asm.index'));
        }
        catch (Exception $e)
        {
            \DB::rollBack();
            Flash::error($e->getMessage());

            return redirect(route('asm.index'));
        }
    }

    /**
     * Remove the specified ASM from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.delete') ;
        $asm = User::find($id);

        if (empty($asm)) {
            Flash::error('ASM not found');

            return redirect(route('asm.index'));
        }

        $asm->delete($id);
        Flash::success('ASM has been deleted successfully.');

        return redirect(route('asm.index'));
    }

    //-------Bulk Import------------

    public function showBulkUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.bulk-upload') ;
        if($request->session()->exists('asm_import')){
            return view('admin.asm.progress') ;
        }
        return view('admin.asm.bulk-upload') ;
    }

    public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.bulk-upload') ;
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
                session()->put('asm_import', $fileWithPath) ;
                session()->put('asm_count', 0) ;
                $unprocessedData=[];
                $i=0;
                $importingFile = fopen($fileWithPath, 'r');
                // $headers = fgetcsv($importingFile);
                $row = [];
                $rowCount = 0;
                while (($rowLine = fgetcsv($importingFile)) !== false) {
                        $i++;
                        if($rowCount > 0){
                            $row = array_map(function ($value) {
                                return str_replace(["\r", "\n"], ' ', $value);
                            }, $rowLine);
                            $validateColumnIndexes = [0,1,2,3];
                            $validateColumnMessages = [
                                0 => 'Name is required',
                                1=> 'Email is required',
                                2=> 'Phone is required',
                                3=> 'Branch Code is required',
                            ];
                            $errorIndexes = [];
                            foreach($validateColumnIndexes as $validateColumnIndex)
                            {
                                if(empty($row[$validateColumnIndex]))
                                {
                                    array_push($errorIndexes, $validateColumnIndex);
                                    break;
                                }
                            }
                            if(count($errorIndexes) == 0){
                                if($user = $this->isASMExist($row[1],$row[2]))
                                {
                                    $allocatedBranches = $this->getBranchIdByCode(explode(',', $row[3]));
                                    if($this->isPhoneExist($row[2],$user->id))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", duplicate phone number found. ");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    else if($this->isEmailExist($row[1],$user->id))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", duplicate email found. ");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    else if($allocatedBranches === null)
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", Invalid Branch Code Found.");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    else if($this->isBranchDuplicate($allocatedBranches, $user->id))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", Duplicate Branch Code Found.");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    else
                                    {
                                        $user->update([
                                            'name'=> $row[0] ?? null,
                                            'email' => $row[1] ?? null,
                                            'phone' => $row[2] ?? null,
                                            'role' =>$this->getReservedRoleId('ASM'),
                                        ]);
                                        Branch::where('asm_user_id', $user->id)->update([
                                            'asm_user_id' => 0,
                                        ]);
                                        foreach($allocatedBranches as $allocatedBranche)
                                        {
                                            $branch = Branch::find($allocatedBranche);
                                            $branch->update([
                                                'asm_user_id' => $user->id,
                                            ]);
                                        }
                                        $count++ ;
                                    }
                                }
                                else
                                {
                                    $allocatedBranches = $this->getBranchIdByCode(explode(',', $row[3]));
                                    if($this->isEmailExist($row[1]))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", Email is Already Exist");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    else if($this->isPhoneExist($row[2]))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", Phone Number is Already Exist");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    else if($allocatedBranches === null)
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", Invalid Branch Code.");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    else if($this->isBranchDuplicate($allocatedBranches))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", Duplicate Branch Code.");
                                        $unProcessedCount++ ;
                                        continue;
                                    }
                                    else
                                    {
                                        $user = User::create([
                                            'name'=> $row[0] ?? null,
                                            'email' => $row[1] ?? null,
                                            'phone' => $row[2] ?? null,
                                            'role' =>$this->getReservedRoleId('ASM'),

                                        ]);
                                        foreach($allocatedBranches as $allocatedBranche)
                                        {
                                            $branch = Branch::find($allocatedBranche);
                                            $branch->update([
                                                'asm_user_id' => $user->id,
                                            ]);
                                        }
                                        $count++ ;
                                    }
                                }
                                session()->put('asm_count', $count) ;

                                // $request->session()->save();
                                // sleep(10) ;

                                if($count == $lineSllice){
                                    $request->session()->save();
                                    sleep(1) ;
                                }
                            }
                            else
                            {
                                array_push($unprocessedData,"<br>In row ".$i.", ".$validateColumnMessages[$errorIndexes[0]]);
                                $unProcessedCount++ ;
                            }
                        }
                        $rowCount++;
                    }
                fclose($importingFile);
                $request->session()->forget('asm_import');

                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('asm_count').' records processed. & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200);

            }


        } catch (\Exception $e) {
            $request->session()->forget('asm_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;


        }



    }

    public function getProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('asm.bulk-upload') ;
        if($request->session()->has('asm_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('asm_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('asm_count').' records processed.'], 200); ;

    }

    public function isASMExist($email,$phone)
    {
       $asm = User::where('role', $this->getReservedRoleId('ASM'))->where('phone', $phone)->orWhere('email',$email)->first() ;
       return $asm ;
    }

    public function isPhoneExist($phoneNumber, $userId = 0)
    {
        $user=User::where('phone',$phoneNumber)->whereNotIn('id', [$userId])->get();
        if(count($user) > 0)
        {
            return true;
        }
        return false;
    }

    public function isEmailExist($email, $userId = 0)
    {
        $user=User::where('email',$email)->whereNotIn('id', [$userId])->get();
        if(count($user) > 0)
        {
            return true;
        }
        return false;
    }

    public function getBranchIdByCode($branchCodes)
    {
        $branchIds = [];
        foreach($branchCodes as $branchCode)
        {
            $branch = Branch::where('branch_code', $branchCode)->first();
            if($branch == null)
            {
                return null;
            }
            array_push($branchIds, $branch->id);
        }
        return $branchIds;
    }
    public function isBranchDuplicate($branchIds, $userId = 0)
    {
        foreach($branchIds as $branchId)
        {
            $branch = Branch::find($branchId);
            if($userId != 0 && $branch->asm_user_id == $userId)
            {
                continue;
            }
            if($branch->asm_user_id != 0)
            {
                return true;
            }
            // $users = User::where('role', $this->getReservedRoleId('ASM'))->whereNotIn('id', [$userId])->get();
            // foreach($users as $user)
            // {
            //     if(empty($user->allocated_branch))
            //     {
            //         continue;
            //     }
            //     $allocated_branch_arr = explode(',', $user->allocated_branch);
            //     if(in_array($branchId, $allocated_branch_arr))
            //     {
            //         return false;
            //     }
            // }
        }
        return false;
    }

    public function export()
    {
        $numberOfRecords = User::where('role', $this->getReservedRoleId('ASM'))->count();
        set_time_limit(0);
        $filename = "ASM_".$this->getUniqueId().".csv";
        $headings = [
            'Name',
            'Email',
            'Phone',
            'Branch Name',
            'Branch Code',
        ];

        $myfile = fopen(public_path("/excel_exports/asm/").$filename, "w");
        fputcsv($myfile,$headings);
        $fetchDataLimit = 1000;
        $fetchDataFrom = 0;
        $i = 0;
        while($i < $numberOfRecords)
        {
            $data = User::with('branch')->where('role', $this->getReservedRoleId('ASM'))->orderBy('id', 'DESC')->skip($fetchDataFrom)->take($fetchDataLimit)->get();
            foreach($data as $val)
            {
                $branches="";
                $branchCodes="";
                $allocated_branches = explode(',', $val->allocated_branches);
                $j = 0;
                foreach($allocated_branches as $allocated_branch)
                {
                    if($j!=0)
                    {
                        $branches.=", ";
                        $branchCodes.=", ";
                    }
                    $branch = Branch::find($allocated_branch);
                    $branches.=$branch->name ?? "";
                    $branchCodes.=$branch->branch_code ?? "";
                    $j++;
                }
                $content = [
                    $val->name,
                    $val->email,
                    $val->phone,
                    implode(', ', Branch::where('asm_user_id', $val->id)->pluck('name')->toArray()) ?? "",
                    implode(', ', Branch::where('asm_user_id', $val->id)->pluck('branch_code')->toArray()) ?? "",
                ];
                fputcsv($myfile,$content);
            }
            $fetchDataFrom += $fetchDataLimit;
            $i += $fetchDataLimit;
        }
        fclose($myfile);
        $filePath = public_path("/excel_exports/asm/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    function addLiftingByASM($lifting)
    {
        //keep log
        $logData = [
            'user_id' => $lifting->user_id,
            'request' => json_encode($lifting),
            'action' => 'Add Lifting By ASM',
            'model_name' => 'Lifting, Lifting approval History, MasonLifting, Reward',
        ];
        $logTable = Log::create($logData);
        //keep log
        try {
            \DB::beginTransaction();
            $dealerAvailableStock =  $this->availStock($lifting->product_id, $lifting->user_id, $lifting->lifting_date);
            $currentMonthLiftings =  $this->getCurrentMonthLifting($lifting->product_id, $lifting->user_id, $lifting->lifting_date) ;

            $lifting->available_stock = $dealerAvailableStock - $currentMonthLiftings;
            $lifting->req_status = 1;
            $lifting->action_taken_at = Carbon::now()->format('y-m-d H:i:s');
            $lifting->action_taken_by = LiftingApprovalHistory::where('lifting_id', $lifting->id)->first()->seek_approval_by ?? 0;
            $lifting->save();

             // As Per Client Requirement It Is Commented Out.
            $isVerified = 0;
            
            // $isVerified = 1;
            // if(($dealerAvailableStock - $currentMonthLiftings) < $lifting->qty)
            // {
            //     $isVerified = 0;
            // }

            $rewards = Reward::where('lifting_id', $lifting->id)->get();
            $masonId = $rewards[0]->user_id;
            $point = 0;
            $bonusPoint = 0;
            foreach($rewards as $reward)
            {
                if($reward->is_bonus == 0){
                    $point = $reward->point;
                }
                else{
                    $bonusPoint = $reward->point;
                }
                $reward->is_verified = $isVerified;
                $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                if($isVerified == Reward::VERIFIED)
                {
                    $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_YES;
                }
                $reward->is_eligible_for_ledger = $isEligibleForLedgerInRewardTable;
                $reward->save();
            }
            $this->updatePoint($masonId);
            \DB::commit();
            $tables = json_encode([
                'Lifting' => $lifting,
                'Reward' => $rewards,
            ]);
            $logTable->update([
                'response' => $tables
            ]);
            // if(($dealerAvailableStock - $currentMonthLiftings)< $lifting->qty)
            // {
            //     Flash::error('Lifting is rejected, please contact admin.');
            //     return redirect(route('dealer.pending.liftings'));

            // }else
            // {

            //To keep approved history record.
            $liftingApprovalHistory = [
                'lifting_id' => $lifting->id,
                'qty' => $lifting->qty,
                'point' => $point,
                'bonus_point' => $bonusPoint,
                'seek_approval' => 4,
                'seek_approval_by' => $lifting->action_taken_by,
                'seek_approval_from' => $lifting->seek_approval_from,
                'approval_window' => $this->settingVal('setting_name', 'asm_approval_window'),
                'action_status' => 3,
                'action_taken_by' => $lifting->action_taken_by,
            ];
            LiftingApprovalHistory::create($liftingApprovalHistory);
            return [
                "status" => true,
                "msg" => 'lifting Accepted.',
            ];
            // }
        } catch (Exception $e) {
            \DB::rollBack();
            $logTable->update([
                'response' => $e->getMessage()
            ]);
            return [
                "status" => false,
                "msg" =>$e->getMessage(),
            ];
        }

    }
    function rejectLiftingByASM($lifting)
    {
        //keep log
        $logData = [
            'user_id' => $lifting->user_id,
            'request' => json_encode($lifting),
            'action' => 'Reject Lifting By ASM',
            'model_name' => 'Lifting, Lifting approval History, MasonLifting, Reward',
        ];
        $logTable = Log::create($logData);
        //keep log
        try {
            \DB::beginTransaction();
            $lifting->req_status = 2;
            $lifting->action_taken_at = Carbon::now()->format('y-m-d H:i:s');
            $lifting->action_taken_by = LiftingApprovalHistory::where('lifting_id', $lifting->id)->first()->seek_approval_by ?? 0;
            $lifting->save();
            //To keep reject history record.
            $rewards = Reward::where('lifting_id', $lifting->id)->get();
            $point = 0;
            $bonusPoint = 0;
            foreach($rewards as $reward)
            {
                if($reward->is_bonus == 0){
                    $point = $reward->point;
                }
                else{
                    $bonusPoint = $reward->point;
                }

                //Reward Status Is Verified to Be Updated to Rejected (Code 2) .
                $reward->is_verified = 2;
                $reward->save() ;
            }
            $liftingApprovalHistory = [
                'lifting_id' => $lifting->id,
                'qty' => $lifting->qty,
                'point' => $point,
                'bonus_point' => $bonusPoint,
                'seek_approval' => 4,
                'seek_approval_by' => $lifting->action_taken_by,
                'seek_approval_from' => $lifting->seek_approval_from,
                'approval_window' => $this->settingVal('setting_name', 'asm_approval_window'),
                'action_status' => 4,
                'action_taken_by' => $lifting->action_taken_by,
            ];
            LiftingApprovalHistory::create($liftingApprovalHistory);

          


            \DB::commit();
            $tables = json_encode([
                'Lifting' => $lifting,
                'Reward' => $rewards,
            ]);
            $logTable->update([
                'response' => $tables
            ]);
            return [
                'status' => true,
                'msg' => 'Lifting Rejected Successfully.',
            ];
        } catch (Exception $e) {
            \DB::rollBack();
            $logTable->update([
                'response' => $e->getMessage()
            ]);
            return [
                "status" => false,
                "msg" =>$e->getMessage(),
            ];
        }

    }

    public function liftingActionTaken(Request $request)
    {
        $action=decrypt($request->taken);
        $liftingId=decrypt($request->lifting);
        $msg = "";
        $status = 0;
        $proceedToAction = true;
        $lifting = Lifting::find($liftingId);
        if($lifting == null)
        {
            $msg = "Invalid Link";
            $proceedToAction = false;
        }
        if($lifting->seek_approval != 4)
        {
            $msg = "Invalid Link";
            $proceedToAction = false;
        }
        if($lifting->req_status != 0)
        {
            $msg = "Action Already Taken";
            $proceedToAction = false;
        }
        if($proceedToAction)
        {
            if($action == 1)
            {
                $response = $this->addLiftingByASM($lifting);
                if($response['status'])
                {
                    $msg = "Lifting Accepted Successfully.";
                    $status = 1;
                }
                else
                {
                    $msg = $response['msg'];
                    $status = 0;
                }
            }
            else if($action == 0)
            {
                $response = $this->rejectLiftingByASM($lifting);
                if($response['status'])
                {
                    $msg = "Lifting Rejected Successfully.";
                    $status = 2;
                }
                else
                {
                    $msg = $response['msg'];
                    $status = 0;
                }
            }
            else
            {
                $msg = "Invalid Link";
            }
        }
        return view('asm-success')->with([
            'status' => $status,
            'liftingId' => $liftingId,
            'msg' => $msg,
        ]);
    }

}
