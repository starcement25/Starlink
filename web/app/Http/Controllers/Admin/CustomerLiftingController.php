<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Models\CustomerLifting;
use App\Exports\CustomerLiftingExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\DataTables\CustomerLiftingDataTable;
use App\Repositories\CustomerLiftingRepository;
use App\Http\Requests\CustomerLifting\CreateCustomerLiftingRequest;
use App\Http\Requests\CustomerLifting\UpdateCustomerLiftingRequest;
use App\Traits\HelperTrait;

class CustomerLiftingController extends Controller
{
    use HelperTrait;
    /** @var CustomerLiftingController $liftingRepository*/
    private $liftingRepository;

    public function __construct(CustomerLiftingRepository $customerLiftingRepo)
    {
        $this->liftingRepository = $customerLiftingRepo;
    }

    /**
     * Display a listing of the Dealer.
     */
    public function index(CustomerLiftingDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.view') ;
        return $dataTable->render('admin.customer-lifting.index');
       
    }

    /**
     * Show the form for creating a new Customer Lifting.
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.create') ;
        $dealerArr = User::select('id', 'name')->whereIn('role', ['3','4'])->pluck('name', 'id')->toArray();
        $dealers   = ['' => 'Select Dealer'] + $dealerArr ;
        $monthOption = ['' => 'Select Month',
                        '1'=> 'January',
                        '2'=> 'February',
                        '3'=> 'March',
                        '4'=> 'April',
                        '5'=> 'May',
                        '6'=> 'June',
                        '7'=> 'July',
                        '8'=> 'August',
                        '9'=> 'September',
                        '10'=> 'October',
                        '11'=> 'November',
                        '12'=> 'December',
                        ]  ;
        $productOption = ['' => 'Select Product'] +  Product::orderBy('name', 'ASC')->pluck('name', 'id')->toArray() ;

        return view('admin.customer-lifting.create')->with('dealerOption', $dealers)->with('monthOption', $monthOption)
                ->with('productOption', $productOption) ;
                
   
      //  return view('admin.dealers.create');
    }

    /**
     * Store a newly created Dealer in storage.
     */
    public function store(CreateCustomerLiftingRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.create') ;
        $input   = $request->all();
        $check = CustomerLifting::where([
            'dealer_id' => $input['dealer_id'],
            'product_id' => $input['product_id'],
            'month' => $input['month'],
            'year' => $input['year']
        ])->get();
        if(!$check->isEmpty())
        {
            Flash::error('This Record Already Taken.');
            return redirect(route('customer-stock.index'));
        }
        $lifting = $this->liftingRepository->create($input);
        $liftingCode ='L'. str_pad($lifting->id, 5, '0', STR_PAD_LEFT) ;

        $lifting->update([
            'lifting_code' => $liftingCode,
        ]);

        Flash::success('Customer Lifting saved successfully.');

        return redirect(route('customer-stock.index'));
    }

    /**
     * Display the specified Dealer.
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.view') ;
        $customerLifting = CustomerLifting::find($id) ;

        if (empty($customerLifting)) {
            Flash::error('Customer Lifting not found');
            return redirect(route('customer-stock.index'));

        }

       // return view('admin.dealers.show')->with('dealer', $dealer);
    }

    /**
     * Show the form for editing the specified Dealer.
    */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.edit') ;
        $customerLifting = CustomerLifting::find($id) ;
        if(empty($customerLifting)){
            abort(404) ;
        }
        $dealerArr = User::select('id', 'name')->whereIn('role', ['3','4'])->pluck('name', 'id')->toArray();
        $dealers   = ['' => 'Select Dealer'] + $dealerArr ;
        $monthOption = ['' => 'Select Month',
                        '1'=> 'January',
                        '2'=> 'February',
                        '3'=> 'March',
                        '4'=> 'April',
                        '5'=> 'May',
                        '6'=> 'June',
                        '7'=> 'July',
                        '8'=> 'August',
                        '9'=> 'September',
                        '10'=> 'October',
                        '11'=> 'November',
                        '12'=> 'December',
                        ]  ;
        $productOption = ['' => 'Select Product'] +  Product::orderBy('name', 'ASC')->pluck('name', 'id')->toArray() ;

        return view('admin.customer-lifting.edit')->with('dealerOption', $dealers)->with('monthOption', $monthOption)
                ->with('productOption', $productOption)->with('customerLifting', $customerLifting) ;
    }

    /**
     * Update the specified Customer Lifting in storage.
     */
    public function update($id, UpdateCustomerLiftingRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.edit') ;
        $customerLifting = $this->liftingRepository->find($id);

        if (empty($customerLifting)) {
            Flash::error('Customer Lifting not found');

            return redirect(route('customer-stock.index'));
        }

        $this->liftingRepository->update($request->all(), $id);

        Flash::success('Customer lifting updated successfully.');

        return redirect(route('customer-stock.index'));
    }

    /**
     * Remove the specified Dealer from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.delete') ;
        $customerLifting = $this->liftingRepository->find($id);

        if (empty($customerLifting)) {
            Flash::error('Customer liftings not found');

            return redirect(route('customer-stock.index'));
        }

        $this->liftingRepository->delete($id);

        Flash::success('Customer lifting deleted successfully.');

        return redirect(route('customer-stock.index'));
    }

    public function showBulkUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.bulk-upload') ;
        if($request->session()->exists('customer_lifting_import')){
            return view('admin.customer-lifting.progress') ;
        }
       
        return view('admin.customer-lifting.bulk-upload') ;
      
    }

    public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.bulk-upload') ;
        set_time_limit(0);
        try {

            if($request->hasFile('csvFile')){
                $file       = $request->file('csvFile');
                $folderPath = \Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  \Storage::disk('public')->path($folderPath) ;
                $records = array_map('str_getcsv', file($fileWithPath));
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = $unProcessedCount = 0;
              //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
                session()->put('customer_lifting_import', $fileWithPath);
                session()->put('customer_lifting_count', 0) ;
                $unprocessedData=[];
                $i=1;
                foreach ($records as $key => $row) {
                        if($key > 0){
                            if(!empty($row[1])){
                                
                                if($user = $this->checkDealerExistenceBySapCode($row[1])){
                                   // $user = $this->isDealerExist($row[1]);

                                   $check = CustomerLifting::where([
                                    'dealer_id' => $user->id,
                                    'product_id' => $row[2],
                                    'month' => $row[3],
                                    'year' =>  $row[4]
                                    ])->get();
                                    
                                    if(!$check->isEmpty())
                                    {
                                        array_push($unprocessedData,"<br>Data of row ".$i." already exist");
                                        $unProcessedCount++ ; 
                                    }
                                    else
                                    {
                                        $data = CustomerLifting::where('lifting_code', $row[0])->first();


                                        if(!empty($data)){
                                            $data->update([
                                                'lifting_code'=> mb_convert_encoding($row[0],'UTF-8', 'ISO-8859-1'),
                                                'dealer_id'=> mb_convert_encoding($user->id,'UTF-8', 'ISO-8859-1'),
                                                'product_id'=> mb_convert_encoding($row[2],'UTF-8', 'ISO-8859-1'),
                                                'month'=> mb_convert_encoding($row[3],'UTF-8', 'ISO-8859-1'),
                                                'year'=> mb_convert_encoding($row[4],'UTF-8', 'ISO-8859-1'),
                                                'quantity'=> mb_convert_encoding($row[5],'UTF-8', 'ISO-8859-1'),
                                                'status'=> mb_convert_encoding($row[6],'UTF-8', 'ISO-8859-1'),
                                            ]);
                                        
                                        }else{
                                            
                                        CustomerLifting::create([
                                                'lifting_code'=> mb_convert_encoding($row[0],'UTF-8', 'ISO-8859-1'),
                                                'dealer_id'=> mb_convert_encoding($user->id ?? NULL,'UTF-8', 'ISO-8859-1'),
                                                'product_id'=> mb_convert_encoding($row[2],'UTF-8', 'ISO-8859-1'),
                                                'month'=> mb_convert_encoding($row[3],'UTF-8', 'ISO-8859-1'),
                                                'year'=> mb_convert_encoding($row[4],'UTF-8', 'ISO-8859-1'),
                                                'quantity'=> mb_convert_encoding($row[5],'UTF-8', 'ISO-8859-1'),
                                                'status'=> mb_convert_encoding($row[6],'UTF-8', 'ISO-8859-1'),
                                        ]);
                                        }
                                        $count++ ;
                                    }
                                }
                                else{
                                    array_push($unprocessedData,"<br>In row ".$i." invalid sap code");
                                    $unProcessedCount++ ; 
                                }
                                
                                session()->put('customer_lifting_count', $count) ;
                                
                                // $request->session()->save();
                                // sleep(10) ;
    
                                if($count == $lineSllice){
                                    $request->session()->save();
                                    sleep(1) ;
                                }
                               
                              //  echo session()->get('customer_lifting_count');
                               
                            
                            }
                           
                        
                        }
                        $i++;
                    }
                 $request->session()->forget('customer_lifting_import');
                
                 
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('customer_lifting_count').' records processed & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200);
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('customer_lifting_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }
    
    public function getProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.bulk-upload') ;
        if($request->session()->has('customer_lifting_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('customer_lifting_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('customer_lifting_count').' records processed'], 200); ;

    }
    public function getDealerByCode($code = "")
    {
      $user =  User::where('emp_code', $code)->first() ;
       return $user->id  ?? null;
    }

    public function isDealerExist($empCode)
    {
       $user = User::where('emp_code', $empCode)->first() ;
       return $user ;
    }
    public function checkDealerExistenceBySapCode($sapCode)
    {
       $user = User::where('sap_code', $sapCode)->first() ;
       return $user ;
    }

    public function customerStockExport() 
    {
        $numberOfRecords = CustomerLifting::count();
        set_time_limit(0);
        $filename = "Customer_Lifting_".$this->getUniqueId().".csv";
        $headings = [
            "Code",	
            "Dealer Name",
            "Dealer Code",
            "Year",
            "Branch",
            "Month",
            "Linked Dealer",
            "Product Name",
            "Quantity",
            "Status"
        ];
        
        $myfile = fopen(public_path("/excel_exports/customer_liftings/").$filename, "w");
        fputcsv($myfile,$headings);
        $fetchDataLimit = 1000;
        $fetchDataFrom = 0;
        $i = 0;
        while($i < $numberOfRecords)
        {
            $data = CustomerLifting::with(['dealer','dealer.branch', 'dealer.dealer_linked'])->with('product')->orderBy('id', 'DESC')->select([
                'customer_liftings.*',
                \DB::raw("(
                    CASE WHEN customer_liftings.month = 1 THEN 'January' 
                    WHEN customer_liftings.month = 2 THEN 'February'
                    WHEN customer_liftings.month = 3 THEN 'March'
                    WHEN customer_liftings.month = 4 THEN 'April'
                    WHEN customer_liftings.month = 5 THEN 'May'
                    WHEN customer_liftings.month = 6 THEN 'June'
                    WHEN customer_liftings.month = 7 THEN 'July'
                    WHEN customer_liftings.month = 8 THEN 'August'
                    WHEN customer_liftings.month = 9 THEN 'September'
                    WHEN customer_liftings.month = 10 THEN 'October'
                    WHEN customer_liftings.month = 11 THEN 'November'
                    WHEN customer_liftings.month = 12 THEN 'December'
                    ELSE 0 END) AS `month_name`")
                    
                ])->skip($fetchDataFrom)->take($fetchDataLimit)->get();
            foreach($data as $val)
            {
                $content = [
                    $val->lifting_code ?? "",
                    $val->dealer->name ?? "",
                    $val->dealer->emp_code ?? "",
                    $val->year ?? "",
                    $val->dealer->branch->name ?? "",
                    $val->month_name ?? "",
                    $val->dealer->dealer_linked->name ?? "",
                    $val->product->name ?? "",
                    $val->quantity ?? "",
                    $val->status ==1 ? "Active" : "Disabled" ?? "",
                ];
                fputcsv($myfile,$content);
            }
            $fetchDataFrom += $fetchDataLimit;
            $i += $fetchDataLimit;
        }
        fclose($myfile);
        $filePath = public_path("/excel_exports/customer_liftings/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
        // return Excel::download(new CustomerLiftingExport, 'CustomerLifting.xlsx');
        //return (new CustomerLiftingExport)->store('CustomerLifting.xlsx');
        // return count($data);
    }

    public function updatingCustomerLiftingQuantity()
    {
        return "Service Blocked By Devs";
        \Helper::checkIsUserAuthorizeToPerformTheTask('customer-stock.bulk-upload') ;
        set_time_limit(0);
        try {

                
            $fileWithPath =  "/var/www/html/web/public/Addtional stock upload.csv";
            $records = array_map('str_getcsv', file($fileWithPath));
            $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
            $count = $unProcessedCount = 0;
            session()->put('customer_lifting_import', $fileWithPath);
            session()->put('customer_lifting_count', 0) ;
            $unprocessedData=[];
            $i=1;
            foreach ($records as $key => $row) {
                    if($key > 0){
                            
                        if($user = $this->checkDealerExistenceBySapCode($row[0])){
                            // $user = $this->isDealerExist($row[1]);

                            $check = CustomerLifting::where([
                            'dealer_id' => $user->id,
                            'product_id' => $row[1],
                            'month' => $row[2],
                            'year' =>  $row[3]
                            ])->first();
                            
                            if(!empty($check))
                            {
                                
                                echo "<br>In row ".$i.", current is ". $check->quantity ." and after update ".($check->quantity + $row[4])."<br>";
                                echo "<br>id id ".$check->id."<br>";
                                // CustomerLifting::where('id', $check->id)->update([
                                //     "quantity" => ($check->quantity + $row[4])
                                // ]);
                                $count++ ;
                            }
                            else
                            {
                                array_push($unprocessedData,"<br>Data of row ".$i." already exist");
                                $unProcessedCount++ ; 
                            }
                        }
                        else{
                            array_push($unprocessedData,"<br>In row ".$i." invalid sap code - ".$row[0]);
                            $unProcessedCount++ ; 
                        }
                        
                    
                    }
                    $i++;
                }
            
                
            // return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.$count.' records processed & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200);
            echo 'Import Successfull '.$count.' records processed & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData);
    
            
        } catch (\Exception $e) {
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }

}
