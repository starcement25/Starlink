<?php

namespace App\Http\Controllers\Admin;

use Flash;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\Dealer;
use App\Models\Process;
use Illuminate\Http\Request;
use App\DataTables\DealerDataTable;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Dealer\CreateDealerRequest;
use App\Http\Requests\Dealer\UpdateDealerRequest;

class DealerController extends AppBaseController
{
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
        
        return $dataTable->render('admin.dealers.index');
       
    }

    /**
     * Show the form for creating a new Dealer.
     */
    public function create()
    {
        $rolesArr  = Role::select('id', 'role_name')->whereIn('id',['3','4'])->pluck('role_name', 'id')->toArray();
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
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
        $dealer = $this->dealerRepository->find($id);

        if (empty($dealer)) {
            Flash::error('Dealer not found');

            return redirect(route('dealers.index'));
        }

        $rolesArr  = Role::select('id', 'role_name')->whereIn('id',['3','4'])->pluck('role_name', 'id')->toArray();
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
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
        if($request->session()->exists('dealer_import')){
            return view('admin.dealers.progress') ;
        }
       
        return view('admin.dealers.bulk-upload') ;
      
    }
    public function uploadCsvFile(Request $request)
    {
        set_time_limit(0);
        try {

            if($request->hasFile('csvFile')){
                $file       = $request->file('csvFile');
                $folderPath = Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  Storage::disk('public')->path($folderPath) ;
                $records = array_map('str_getcsv', file($fileWithPath));
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = 0;
              //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
                session()->put('dealer_import', $fileWithPath) ;
                session()->put('dealer_count', 0) ;
        
                foreach ($records as $key => $row) {
                        if($key > 0){
                            if(!empty($row[1])){
                                User::create([
                                    'emp_code'=> $row[0] ?? null,
                                    'name' => $row[1] ?? null,
                                    'role' => $row[2] ?? 4,
                                    'linked_dealer' => !empty($row[3]) ? $this->getDealderByCode($row[3]) : null,
                                    'phone' => $row[4] ?? null,
                                    'whatsapp_no' => $row[5] ?? null,
                                    'branch_id' => $row[6] ?? null,
                                    'status' => 1,
                                ]);
                                $count++ ;
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
                    }
                 $request->session()->forget('dealer_import');
                
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('dealer_count').' records procxessed.'], 200); ;
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('dealer_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }
    
    public function getProgress(Request $request)
    {
        if($request->session()->has('dealer_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('dealer_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('dealer_count').' records processed.'], 200); ;

    }
    public function getDealderByCode($code = "")
    {
      $user =  User::where('emp_code', $code)->first() ;
       return $user->id  ?? null;
    }
}
