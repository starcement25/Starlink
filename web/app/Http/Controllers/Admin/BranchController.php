<?php

namespace App\Http\Controllers\Admin;

use Flash;
use App\Models\Zone;
use App\Models\State;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\DataTables\BranchDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\CreateBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;

class BranchController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BranchDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.view') ;
        // $branches = Branch::all();
        // return view('admin.branch.index')   ->with('branches', $branches);
        
        return $dataTable->render('admin.branch.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.create') ;
        $zones = Zone::where('status', '1')->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $zones = ['' => 'Select Zone'] + $zones ;

        $states = State::orderBy('state_name', 'DESC')->pluck('state_name', 'id')->toArray();
        $states = ['' => 'Select State'] + $states ;
        
        return view('admin.branch.create')->with('zoneOption', $zones)
                                        ->with('zoneOptionSelected', "")
                                        ->with('stateOption', $states)
                                        ->with('stateOptionSelected', "")
                                        ->with('statusSelected',"");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBranchRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.create') ;
        $input = $request->all();
       
        $Branch = Branch::create($input);
        Flash::success('Branch saved successfully.');

        return redirect(route('branch.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.view') ;
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.edit') ;
        $branch = Branch::find($id);
        $zones = Zone::where('status', '1')->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $zones = ['' => 'Select Zone'] + $zones ;

        $states = State::orderBy('state_name', 'DESC')->pluck('state_name', 'id')->toArray();
        $states = ['' => 'Select State'] + $states ;

        if (empty($branch)) {
            Flash::error('Branch not found');
            return redirect(route('branch.index'));
        }

        return view('admin.branch.edit')->with('branch', $branch)
                                        ->with('zoneOption', $zones)
                                        ->with('zoneOptionSelected', $branch->zone_id)
                                        ->with('stateOption', $states)
                                        ->with('stateOptionSelected', $branch->state_id)
                                        ->with('statusSelected', $branch->status);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBranchRequest $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.edit') ;
        $branch = Branch::find($id);

        if (empty($branch)) {
            Flash::error('Branch not found');
            return redirect(route('branch.index'));
        }

        $branch =  $branch->update($request->all());;

        Flash::success('Branch updated successfully.');

        return redirect(route('branch.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.delete') ;
        $branch = Branch::find($id);
        if (empty($branch)) {
            Flash::error('Branch not found');
            return redirect(route('branch.index'));
        }

        $branch->delete();

        Flash::success('Branch deleted successfully.');
        return redirect(route('branch.index'));
    }

    public function showBulkUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.bulk-upload') ;
        if($request->session()->exists('branch_import')){
            return view('admin.branch.progress') ;
        }
        $states=State::all();
       // return "Hi";
        return view('admin.branch.bulk-upload')->with('states',$states) ;
       //return redirect(route('employee.upload.show'));
    }
    public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.bulk-upload') ;
        set_time_limit(0);
        try {

            if($request->hasFile('csvFile')){
                $file       = $request->file('csvFile');
                $folderPath = \Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  \Storage::disk('public')->path($folderPath) ;
                $records = array_map('str_getcsv', file($fileWithPath));
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = 0; $unProcessedCount = 0;
              //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
                session()->put('branch_import', $fileWithPath) ;
                session()->put('total_count', 0) ;
        
                foreach ($records as $key => $row) {
                        if($key > 0){
                            if(!empty($row[0])){
                                $branch = Branch::where('branch_code', $row[0])->first() ;
                                Branch::updateOrCreate(
                                    ['id' => $branch->id ?? null],
                                    [
                                        'branch_code'=> $row[0] ?? null,
                                        'name' => $row[1] ?? null,
                                        'zone_id' => $row[2] ?? null,
                                        'state_id' => $row[3] ?? null,
                                        'description' => $row[4] ?? null,
                                        'status' => $row[5] ?? null,
                                    ]
                                );
                                $count++ ;
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
                 $request->session()->forget('branch_import');
                
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('total_count').' records processed.'], 200); ;
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('branch_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }
    
    public function getProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('branch.bulk-upload') ;
        if($request->session()->has('branch_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('total_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('total_count').' records processed.'], 200); ;

    }
}

