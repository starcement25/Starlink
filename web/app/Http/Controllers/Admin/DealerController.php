<?php

namespace App\Http\Controllers\Admin;

use Flash;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\Dealer;
use App\Models\Process;
use App\Exports\DealerExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\DataTables\DealerDataTable;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Dealer\CreateDealerRequest;
use App\Http\Requests\Dealer\UpdateDealerRequest;
use App\Traits\HelperTrait;

class DealerController extends AppBaseController
{

    use HelperTrait;

    /** @var DealerRepository $dealerRepository*/
    private $dealerRepository;

    public function __construct(UserRepository $dealerRepo)
    {
        $this->dealerRepository = $dealerRepo;
    }

    /**
     * Display a listing of the Dealer.
     */
    public function index(DealerDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.view') ;
        return $dataTable->render('admin.dealers.index');
       
    }

    /**
     * Show the form for creating a new Dealer.
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.create') ;
        $rolesArr  = Role::select('id', 'role_name')->whereIn('id',['3','4','6'])->pluck('role_name', 'id')->toArray();
        $branchArr = Branch::select('id', 'name')->where('status', 1)->pluck('name', 'id')->toArray();
        $dealerArr = User::select('id', 'name')->where('role', '3')->pluck('name', 'id')->toArray();
        
        $branches = ['' => 'Select Branch'] + $branchArr ;
        $roles    = ['' => 'Select Type'] + $rolesArr ;
        $dealers    = ['' => 'Select Dealer'] + $dealerArr ;
        $status = [''=> 'Select Status', '1'=> 'Active' , '0'=> 'Disabled'] ;
        return view('admin.dealers.create')
                ->with('statusOption', $status)->with('statusSelected', "")
                ->with('roleOption', $roles)->with('roleSelected', "")
                ->with('branchOption', $branches)->with('branchSelected', "") 
                ->with('dealerOption', $dealers)->with('dealerSelected', "") ;
   
      //  return view('admin.dealers.create');
    }

    /**
     * Store a newly created Dealer in storage.
     */
    public function store(CreateDealerRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.create') ;
        $input = $request->all();

        $dealer = $this->dealerRepository->create($input);

        Flash::success('Dealer saved successfully.');

        return redirect(route('dealers.index'));
    }

    /**
     * Display the specified Dealer.
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.view') ;
        $dealer = $this->dealerRepository->find($id);

        if (empty($dealer)) {
            Flash::error('Dealer not found');
            return redirect(route('dealers.index'));

        }

        return view('admin.dealers.show')->with('dealer', $dealer);
    }

    /**
     * Show the form for editing the specified Dealer.
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.edit') ;
        $dealer = $this->dealerRepository->find($id);

        if (empty($dealer)) {
            Flash::error('Dealer not found');

            return redirect(route('dealers.index'));
        }

        $rolesArr  = Role::select('id', 'role_name')->whereIn('id',['3','4','6'])->pluck('role_name', 'id')->toArray();
        $branchArr = Branch::select('id', 'name')->where('status', 1)->pluck('name', 'id')->toArray();
        $dealerArr = User::select('id', 'name')->where('role', '3')->pluck('name', 'id')->toArray();
        
        $branches = ['' => 'Select Branch'] + $branchArr ;
        $roles    = ['' => 'Select Type'] + $rolesArr ;
        $dealers    = ['' => 'Select Dealer'] + $dealerArr ;
        $status = [''=> 'Select Status', '1'=> 'Active' , '0'=> 'Disabled'] ;
        return view('admin.dealers.edit')
                ->with('dealer', $dealer)
                ->with('statusOption', $status)->with('statusSelected', $dealer->status)
                ->with('roleOption', $roles)->with('roleSelected', $dealer->role)
                ->with('branchOption', $branches)->with('branchSelected', $dealer->branch_id) 
                ->with('dealerOption', $dealers)->with('dealerSelected', $dealer->linked_dealer) ;
    }

    /**
     * Update the specified Dealer in storage.
     */
    public function update($id, UpdateDealerRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.edit') ;
        $dealer = $this->dealerRepository->find($id);

        if (empty($dealer)) {
            Flash::error('Dealer not found');

            return redirect(route('dealers.index'));
        }

        $dealer = $this->dealerRepository->update($request->all(), $id);

        Flash::success('Dealer updated successfully.');

        return redirect(route('dealers.index'));
    }

    /**
     * Remove the specified Dealer from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.delete') ;
        $dealer = $this->dealerRepository->find($id);

        if (empty($dealer)) {
            Flash::error('Dealer not found');

            return redirect(route('dealers.index'));
        }

        $this->dealerRepository->delete($id);

        Flash::success('Dealer deleted successfully.');

        return redirect(route('dealers.index'));
    }
    public function showBulkUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.bulk-upload') ;
        if($request->session()->exists('dealer_import')){
            return view('admin.dealers.progress') ;
        }
       
        return view('admin.dealers.bulk-upload') ;
      
    }
    public function showSapUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.sap-upload') ;
        if($request->session()->exists('dealer_sap_import')){
            return view('admin.dealers.progress') ;
        }
       
        return view('admin.dealers.sap-upload') ;
      
    }
    public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.bulk-upload') ;
        set_time_limit(0);
        try {

            if($request->hasFile('csvFile')){
                $file       = $request->file('csvFile');
                $folderPath = Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  Storage::disk('public')->path($folderPath) ;
                $records = array_map('str_getcsv', file($fileWithPath));
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = $unProcessedCount = 0;
              //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
                session()->put('dealer_import', $fileWithPath) ;
                session()->put('dealer_count', 0) ;
                $unprocessedData=[];
                $i=1;
                foreach ($records as $key => $row) {
                        if($key > 0){
                            if(!empty($row[1])){

                                if($user = $this->isDealerExist($row[0])){

                                    $data = User::where('phone', $row[4])->whereNotIn('id', [$user->id])->first();
                                    if(empty($data)){
                                        if(\Helper::isBranchExist($this->getBranchIdByCode($row[6]) ?? null))
                                        {
                                            $user->update([
                                                'emp_code'=> mb_convert_encoding($row[0] ?? null,'UTF-8', 'ISO-8859-1'),
                                                'name' => mb_convert_encoding($row[1] ?? null,'UTF-8', 'ISO-8859-1'),
                                                'role' => mb_convert_encoding($row[2] ?? 4,'UTF-8', 'ISO-8859-1'),
                                                'linked_dealer' => !empty($row[3]) ? $this->getDealerByCode($row[3]) : null,
                                                'phone' => mb_convert_encoding($row[4] ?? null,'UTF-8', 'ISO-8859-1'),
                                                'whatsapp_no' => mb_convert_encoding($row[5] ?? null,'UTF-8', 'ISO-8859-1'),
                                                'branch_id' => $this->getBranchIdByCode($row[6]) ?? null,
                                                'status' => mb_convert_encoding($row[7] ?? null,'UTF-8', 'ISO-8859-1'),
                                            ]);
                                            $count++ ;
                                        }else{
                                            array_push($unprocessedData,"<br>In row ".$i." invalid branch code");
                                            $unProcessedCount++ ;
                                        }
                                           
                                    }else{
                                        array_push($unprocessedData,"<br>In row ".$i." Phone number already exist");
                                        $unProcessedCount++ ;
                                    }

                                }else{
                                    
                                    $data = User::where('phone', $row[4])->first();
                                    
                                    if(empty($data)){
                                        if(\Helper::isBranchExist($this->getBranchIdByCode($row[6]) ?? null))
                                        {
                                            User::create([
                                            'emp_code'=> mb_convert_encoding($row[0] ?? null,'UTF-8', 'ISO-8859-1'),
                                            'name' => mb_convert_encoding($row[1] ?? null,'UTF-8', 'ISO-8859-1'),
                                            'role' => mb_convert_encoding($row[2] ?? 4,'UTF-8', 'ISO-8859-1'),
                                            'linked_dealer' => !empty($row[3]) ? $this->getDealerByCode($row[3]) : null,
                                            'phone' => mb_convert_encoding($row[4] ?? null,'UTF-8', 'ISO-8859-1'),
                                            'whatsapp_no' =>mb_convert_encoding( $row[5] ?? null,'UTF-8', 'ISO-8859-1'),
                                            'branch_id' => $this->getBranchIdByCode($row[6]) ?? null,
                                            'status' => mb_convert_encoding($row[7] ?? null,'UTF-8', 'ISO-8859-1'),
                                            ]);
                                            $count++ ;
                                        }else{
                                            array_push($unprocessedData,"<br>In row ".$i." invalid branch code");
                                            $unProcessedCount++ ;
                                        }
                                            
                                       }
                                       else{
                                            array_push($unprocessedData,"<br>In row ".$i." Phone number already exist");
                                           $unProcessedCount++ ;
                                        }

                                }
                               
                                
                                session()->put('dealer_count', $count) ;
                                
                                // $request->session()->save();
                                // sleep(10) ;
    
                                if($count == $lineSllice){
                                    $request->session()->save();
                                    sleep(1) ;
                                }
                               
                              //  echo session()->get('dealer_count');
                               
                            
                            }
                           
                        
                        }
                        $i++;
                    }
                 $request->session()->forget('dealer_import');
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('dealer_count').' records processed & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200);
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('dealer_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }

    public function uploadSapCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.sap-upload') ;
        set_time_limit(0);
        try {

            if($request->hasFile('csvFile')){
                $file       = $request->file('csvFile');
                $folderPath = Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  Storage::disk('public')->path($folderPath) ;
                $records = array_map('str_getcsv', file($fileWithPath));
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = $unProcessedCount = 0;
              //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
                session()->put('dealer_sap_import', $fileWithPath) ;
                session()->put('dealer_sap_count', 0) ;
                $unprocessedData=[];
                $i=1;
                foreach ($records as $key => $row) {
                        if($key > 0){
                            if(!empty($row[1])){

                                if($user = $this->isDealerExist($row[0])){
                                    if($this->isSapCodeExist($row[1]))
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i." SAP Code already Exists.");
                                        $unProcessedCount++ ;
                                    }
                                    else
                                    {
                                        $user->update([
                                            'sap_code'=> mb_convert_encoding($row[1] ?? null,'UTF-8', 'ISO-8859-1'),
                                        ]);
                                        $count++ ;
                                    }

                                }else{
                                    array_push($unprocessedData,"<br>In row ".$i." Dealer Code Does Not Exist.");
                                    $unProcessedCount++ ;

                                }
                               
                                
                                session()->put('dealer_sap_count', $count) ;
                                
                                // $request->session()->save();
                                // sleep(10) ;
    
                                if($count == $lineSllice){
                                    $request->session()->save();
                                    sleep(1) ;
                                }
                               
                              //  echo session()->get('dealer_count');
                               
                            
                            }
                           
                        
                        }
                        $i++;
                    }
                 $request->session()->forget('dealer_sap_import');
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'SAP Code Imported Successfull '.session()->get('dealer_sap_count').' records processed & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200);
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('dealer_sap_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Errorssss: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }
    
    public function getProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.bulk-upload') ;
        if($request->session()->has('dealer_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('dealer_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('dealer_count').' records processed.'], 200); ;

    }

    public function getSapProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealers.sap-upload') ;
        if($request->session()->has('dealer_sap_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('dealer_sap_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('dealer_sap_count').' records processed.'], 200); ;

    }

    public function getDealerByCode($code = "")
    {
      $user =  User::where('emp_code', $code)->first() ;
       return $user->id  ?? null;
    }

    public function isDealerExist($empCode)
    {
       $user = User::where('emp_code', $empCode)->whereIn('role',[3,4,6])->first() ;
       return $user ;
    }
    public function isBranchExistByName($branchName)
    {
       $branch = Branch::where('name', $branchName)->first();
       return $branch ;
    }
    public function isSapCodeExist($sapCode)
    {
       $user = User::where('sap_code', $sapCode)->whereIn('role',[3,4,6])->first() ;
       return $user ;
    }
    public function getDealersByBranch($branchId)
    {
        $dealers = User::select('id', 'name')->whereIn('role', ['3','4'])->where('branch_id',$branchId)->get();
        if(!is_null($dealers))
        {
            return response()->json([
                'status'=> true,
                'message' => 'Dealers Found',
                'data' => $dealers,   
            ]);
        }
        else
        {
            return response()->json([
                'status'=> false,
                'message' => 'Dealers Not Found',
                  
            ]);
        }
    }

    public function dealerExport() 
    {
        return Excel::download(new DealerExport, 'dealer.xlsx');
    }


    //Rectifing DB Dealer
    public function getRectifiedDealerUploadProgress(Request $request)
    {
        // return "Service blocked by Dev.";
        if($request->session()->has('dealer_rectified_data_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('rectifing_dealer_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('rectifing_dealer_count').' records processed.'], 200); ;

    }
    public function showRectifiedDealerUploadForm(Request $request)
    {
        // return "Service blocked by Dev.";
        if($request->session()->exists('dealer_rectified_data_import')){
            return view('admin.dealers.progress-rectifing-dealers') ;
        }
       
        return view('admin.dealers.rectifing-dealer-upload') ;
      
    }
    public function uploadRectifiedCsvFileToUpdateDealers()
    {
        return "Service blocked by Dev.";
        set_time_limit(0);
        try {
            $fileWithPath =  storage_path("app/public/temp/Star Link - Dealer_RSAR - customer master 26-09.csv");
            $records = array_map('str_getcsv', file($fileWithPath));
            $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
            $count = $unProcessedCount = 0;
            //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
            session()->put('dealer_rectified_data_import', $fileWithPath) ;
            session()->put('rectifing_dealer_count', 0) ;
            $unprocessedData=[];
            $i=1;
            foreach ($records as $key => $row) {
                    if($key > 2){
                        //-----------------For Updating rest of the details excluding branch and linked dealer------------------------------------
                        // if(!empty(trim($row[18])) && trim($row[18]) == "Active")
                        // {
                        //     $dealer = $this->isSapCodeExist(trim($row[1]));
                        //     if(!$dealer)
                        //     {
                        //         array_push($unprocessedData,"<br>In row ".$i." SAP Code Does not Exists.");
                        //         $unProcessedCount++ ;
                        //         continue;
                        //     }
                        //     $updatingData = [
                        //         'status'=> mb_convert_encoding(1,'UTF-8', 'ISO-8859-1'),
                        //     ];
                        //     if(!empty(trim($row[3])))
                        //     {
                        //         $updatingData['emp_code'] = mb_convert_encoding(trim($row[3]),'UTF-8', 'ISO-8859-1');
                        //     }
                        //     if(!empty(trim($row[5])))
                        //     {
                        //         $updatingData['name'] = mb_convert_encoding(trim($row[5]),'UTF-8', 'ISO-8859-1');
                        //     }
                        //     if(!empty(trim($row[7])))
                        //     {
                        //         if(trim($row[7]) == "Dealer")
                        //         { 
                        //             $updatingData['role'] = mb_convert_encoding(3,'UTF-8', 'ISO-8859-1');
                        //         }
                        //         elseif(trim($row[7]) == "RSSD")
                        //         { 
                        //             $updatingData['role'] = mb_convert_encoding(4,'UTF-8', 'ISO-8859-1');
                        //         }
                        //         elseif(trim($row[7]) == "Sub Dealer")
                        //         { 
                        //             $updatingData['role'] = mb_convert_encoding(6,'UTF-8', 'ISO-8859-1');
                        //         }
                        //     }
                            
                        //     if(!empty(trim($row[15])))
                        //     {
                        //         $updatingData['phone'] = mb_convert_encoding(trim($row[15]),'UTF-8', 'ISO-8859-1');
                        //     }
                            

                        //     $dealer->update($updatingData);
                        // }
                        // elseif(!empty(trim($row[18])) && trim($row[18]) == "disabled")
                        // {
                        //     $dealer = $this->isDealerExist(trim($row[2]));
                        //     if(!$dealer)
                        //     {
                        //         array_push($unprocessedData,"<br>In row ".$i." Dealer Code Does not Exists while updating rectifing disables.");
                        //         $unProcessedCount++ ;
                        //         continue;
                        //     }

                        //     $updatingData = [
                        //         'status'=> mb_convert_encoding(0,'UTF-8', 'ISO-8859-1'),
                        //         // 'old_phone'=> mb_convert_encoding($dealer->phone,'UTF-8', 'ISO-8859-1'),
                        //         'old_phone'=> null,
                        //         // 'phone'=> null,
                        //     ];
                        //     if(!empty(trim($row[3])))
                        //     {
                        //         $updatingData['emp_code'] = mb_convert_encoding(trim($row[3]),'UTF-8', 'ISO-8859-1');
                        //     }
                        //     if(!empty(trim($row[5])))
                        //     {
                        //         $updatingData['name'] = mb_convert_encoding(trim($row[5]),'UTF-8', 'ISO-8859-1');
                        //     }
                        //     if(!empty(trim($row[7])))
                        //     {
                        //         if(trim($row[7]) == "Dealer")
                        //         { 
                        //             $updatingData['role'] = mb_convert_encoding(3,'UTF-8', 'ISO-8859-1');
                        //         }
                        //         elseif(trim($row[7]) == "RSSD")
                        //         { 
                        //             $updatingData['role'] = mb_convert_encoding(4,'UTF-8', 'ISO-8859-1');
                        //         }
                        //         elseif(trim($row[7]) == "Sub Dealer")
                        //         { 
                        //             $updatingData['role'] = mb_convert_encoding(6,'UTF-8', 'ISO-8859-1');
                        //         }
                        //     }
                        //     if(!empty(trim($row[15])))
                        //     {
                        //         $updatingData['phone'] = mb_convert_encoding(trim($row[15]),'UTF-8', 'ISO-8859-1');
                        //     }

                        //     $dealer->update($updatingData);
                        // }
                        // else
                        // {
                        //     continue;
                        // }
                        //-----------------End of Updating rest of the details excluding branch and linked dealer------------------------------------
                        //-----------------For Updating branch and linked dealer------------------------------------
                        // $dealer = $this->isSapCodeExist(trim($row[1]));
                        // if(!$dealer)
                        // {
                        //     $dealer = $this->isDealerExist(trim($row[3]));
                        //     if(!$dealer)
                        //     {
                        //         array_push($unprocessedData,"<br>In row ".$i." Dealer Does not Exists.");
                        //         $unProcessedCount++ ;
                        //         continue;
                        //     }
                        // }
                        // $updatingData = [];
                        // if(!empty(trim($row[9])))
                        // {
                        //     $linkedDealer = $this->isSapCodeExist(trim($row[9]));
                        //     if(!$linkedDealer)
                        //     {
                        //         array_push($unprocessedData,"<br>In row ".$i." Linked Dealer code Does not Exists.");
                        //         $unProcessedCount++ ;
                        //         continue;
                        //     }
                        //     $updatingData["linked_dealer"] = $linkedDealer->id;
                        // }
                        // if(!empty(trim($row[13])))
                        // {
                        //     $branch = $this->isBranchExistByName(trim($row[13]));
                        //     if(!$branch)
                        //     {
                        //         array_push($unprocessedData,"<br>In row ".$i." Branch Does not Exists.");
                        //         $unProcessedCount++ ;
                        //         continue;
                        //     }
                        //     $updatingData["branch_id"] = $branch->id;
                        // }
                        // if(count($updatingData) > 0)
                        // {
                        //     $dealer->update($updatingData);
                        // }
                        //-----------------End of Updating branch and linked dealer------------------------------------

                        //-----------------For reverting nullable branch and linked dealer------------------------------------
                        $dealer = $this->isSapCodeExist(trim($row[1]));
                        if(!$dealer)
                        {
                            $dealer = $this->isDealerExist(trim($row[3]));
                            if(!$dealer)
                            {
                                array_push($unprocessedData,"<br>In row ".$i." Dealer Does not Exists.");
                                $unProcessedCount++ ;
                                continue;
                            }
                        }
                        $updatingData = [];
                        if(empty(trim($row[9])) && !empty(trim($row[8])))
                        {
                            $linkedDealer = $this->isDealerExist(trim($row[8]));
                            if(!$linkedDealer)
                            {
                                array_push($unprocessedData,"<br>In row ".$i." Linked Dealer code Does not Exists.");
                                $unProcessedCount++ ;
                                continue;
                            }
                            $updatingData["linked_dealer"] = $linkedDealer->id;
                        }
                        if(empty(trim($row[13])) && !empty(trim($row[12])))
                        {
                            $branch = $this->isBranchExistByName(trim($row[12]));
                            if(!$branch)
                            {
                                array_push($unprocessedData,"<br>In row ".$i." Branch Does not Exists.");
                                $unProcessedCount++ ;
                                continue;
                            }
                            $updatingData["branch_id"] = $branch->id;
                        }
                        if(count($updatingData) > 0)
                        {
                            $dealer->update($updatingData);
                        }
                        //-----------------End of Updating branch and linked dealer------------------------------------

                        $count++ ;
                        
                        

                        // if($count == $lineSllice){
                        //     sleep(1) ;
                        // }
                    }
                    $i++;
                }
                
            return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Rectified Dealers Updated Successfully '.$count.' records processed & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200);
    
            
        } catch (\Exception $e) {
            
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Errorssss: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }
   
}
