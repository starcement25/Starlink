<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\State;
use App\Models\Branch;
use App\Models\Process;
use App\Models\Employee;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Models\EmployeeBranch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\DataTables\EmployeeDataTable;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Traits\HelperTrait;

class EmployeeController extends Controller
{
    use HelperTrait;
    private $count = 0;
    public function index(EmployeeDataTable $dataTable)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.view') ;
      return $dataTable->render('admin.employee.index') ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.create') ;
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches = ['' => 'Select Branch'] + $branchArr ;

        $stateArr = State::pluck('state_name', 'id')->toArray();
        $states = ['' => 'Select State'] + $stateArr ;
        
        return view('admin.employee.create')->with('branchOption', $branches)
                ->with('branchSelected', "")->with('statusSelected', "")
                ->with('stateOption',  $states) ;
               
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateEmployeeRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.create') ;
          $input = $request->except(['branch_id']);
        //   if($request->has('password')){
        //     $input['password'] = Hash::make($input['password']);
        //   }
          $input['role'] = 1; // For Role 1 Is TE.
          DB::beginTransaction();
            $user = User::create($input);
            $this->createEmployeeBranch($request->branch_id, $user->id);
          DB::commit();
          Flash::success('User saved successfully.');
  
          return redirect(route('employees.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.view') ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.edit') ;
        $user = User::find($id);
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches = ['' => 'Select Branch'] + $branchArr ;
        $branchSelected = $user->employee_branch->pluck('id') ;

        $stateArr = State::pluck('state_name', 'id')->toArray();
        $states = ['' => 'Select State'] + $stateArr ;

        //return $branchSelected ;
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('employees.index'));
        }

        return view('admin.employee.edit')
                ->with('user', $user)->with('statusSelected', $user->status) 
                ->with('branchOption', $branches)->with('branchSelected', $branchSelected) 
                ->with('stateOption',  $states)
        ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.edit') ;
        $user = User::find($id);
       
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('employees.index'));
        }
       
        $emailStatus = User::where('email', $request->email)->WhereNotIn('id', [$user->id])->get();

        if(count($emailStatus) > 0){
            return redirect()->back()->withErrors(['email' => 'This email is already taken.'])
                        ->withInput($request->input());
        }

        DB::beginTransaction();
        // $input = !empty($request->password) ? $request->all() : $request->except('password') ;
        
        // if(array_key_exists('password',  $input)){
        //     $input['password'] = Hash::make($input['password']);
        // }


        // Delete All Previous Ids
        $datas = $user->employee_branch->all() ;
        $idToBeDeleted = [] ;
        foreach($datas as $data){
            $idToBeDeleted[] = $data['pivot']['id'] ;
        }

       // return $idToBeDeleted ;

        EmployeeBranch::whereIn('id', $idToBeDeleted)->delete() ;

        // Re Insert New Ids.
        $this->createEmployeeBranch($request->branch_id, $user->id);
        
        $input = $request->except(['branch_id']);
        $user =  $user->update($input);
       
        DB::commit();
        Flash::success('User updated successfully.');

        return redirect(route('employees.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.delete') ;
    }
    public function showBulkUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.bulk-upload') ;
        if($request->session()->exists('employee_import')){
            return view('admin.employee.progress') ;
        }
       // return "Hi";
        return view('admin.employee.bulk-upload') ;
       //return redirect(route('employee.upload.show'));
    }
    
    public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.bulk-upload') ;
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
              //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
                session()->put('employee_import', $fileWithPath) ;
                session()->put('total_count', 0) ;
        
                foreach ($records as $key => $row) {
                        if($key > 0){
                            if(!empty($row[1])){

                                // User::updateOrCreate(
                                //     ['id' => 4],
                                //     [
                                //         'emp_code'=> $row[0] ?? null,
                                //         'name' => $row[1] ?? null,
                                //         'role' => 1,
                                //         'phone' => $row[2] ?? null,
                                //         'email' => $row[3] ?? null,
                                //         'branch_id' => $row[4] ?? null,
                                //         'designation' => $row[5] ?? null,
                                //         'status' => 1
                                //     ]
                                // );

                                if($user = $this->isEmployeeExist($row[0])){
                                    
                                    $data = User::where('phone', $row[2])->whereNotIn('id', [$user->id])->first();
                                
                                    if(empty($data)){
                                        $branches = explode(',', $row[4] );
                                        $temp=[];
                                        foreach($branches as $branch)
                                        {
                                            $temp[]=$this->getBranchIdByCode($branch);
                                        }
                                        $branches=$temp;
                                        $branchIds  =  Branch::pluck('id') ;
                                        $unKnownId = array_diff($branches, $branchIds->toArray());

                                        if(count($unKnownId) == 0){
                                            $datas = $user->employee_branch->all() ;
                                            $idToBeDeleted = [] ;
                                            foreach($datas as $data){
                                                $idToBeDeleted[] = $data['pivot']['id'] ;
                                            }
                                    
                                            EmployeeBranch::whereIn('id', $idToBeDeleted)->delete() ;
                                            
                                            // Re Insert New Ids.
                                            $this->createEmployeeBranch($branches, $user->id);

                                            $user->update([
                                                'emp_code'=> $row[0] ?? null,
                                                'name' => $row[1] ?? null,
                                                'role' => 1,
                                                'phone' => $row[2] ?? null,
                                                'email' => $row[3] ?? null,
                                                'designation' => $row[5] ?? null,
                                                'status' => $row[6] !='' ? $row[6] : 1,
                                                
                                            ]);
                                            $count++ ;
                                        }else{
                                            $unProcessedCount++ ;
                                        }
                                        
                                    }else{
                                        
                                        $unProcessedCount++ ;
                                    }
                                   
                                }else{
                                    $data = User::where('phone', $row[2])->first();
                                    
                                    if(empty($data)){

                                        $branches = explode(',', $row[4] );
                                        $temp=[];
                                        foreach($branches as $branch)
                                        {
                                            $temp[]=$this->getBranchIdByCode($branch);
                                        }
                                        $branches=$temp;
                                        $branchIds  =  Branch::pluck('id') ;
                                        $unKnownId = array_diff($branches, $branchIds->toArray());

                                        if(count($unKnownId) == 0)
                                        {
                                            $user = User::create([
                                                'emp_code'=> $row[0] ?? null,
                                                'name' => $row[1] ?? null,
                                                'role' => 1,
                                                'phone' => $row[2] ?? null,
                                                'email' => $row[3] ?? null,
                                                'designation' => $row[5] ?? null,
                                                'status' => $row[6] != '' ? $row[6] : 1,
                                                
                                            ]);
                                         
                                            // Re Insert New Ids.
                                            $this->createEmployeeBranch($branches, $user->id);
                                            $count++ ;
                                        }else{
                                            $unProcessedCount++ ;
                                         }
                                        
                                       }
                                       else{
                                           $unProcessedCount++ ;
                                        }

                                   
                                }
                                session()->put('total_count', $count) ;
                                
                                // $request->session()->save();
                                // sleep(10) ;
    
                                if($count == $lineSllice){
                                    $request->session()->save();
                                    sleep(1) ;
                                }
                               
                              //  echo session()->get('total_count');
                               
                            
                            }
                           
                        
                        }
                    }
                 $request->session()->forget('employee_import');
                
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('total_count').' records processed. & '.$unProcessedCount.' records unprocessed.'], 200); 
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('employee_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }
    
    public function getProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('employees.bulk-upload') ;
        if($request->session()->has('employee_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('total_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('total_count').' records procxessed.'], 200); ;

    }

    public function isEmployeeExist($empCode)
    {
       $user = User::where('emp_code', $empCode)->first() ;
       return $user ;
    }

    public function createEmployeeBranch($branches, $userId)
    {
        foreach($branches as $key => $branchId)
        {
            EmployeeBranch::create([
                'user_id' => $userId,
                'branch_id'=> $branchId,
            ]);
        
        }
    }
}
