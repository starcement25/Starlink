<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Catalogue;
use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RejectedRedeemtion;
use App\Http\Controllers\Controller;
use App\DataTables\RedeemtionDataTable;
use App\Models\UserCatalogueRedeemtion;
use App\Notifications\StarLinkNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Redeemtion\UpdateRedeemtionRequest;
use Carbon\Carbon; 

class RedeemtionController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RedeemtionDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.view') ;
        return $dataTable->render('admin.redeemtion.index') ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.create') ;
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.create') ;
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.view') ;
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.edit') ;
        $redeemtion = UserCatalogueRedeemtion::with('catalogue')->with('user')->findOrFail($id);
        $orderPendingReasons = DB::table('order_pending_reason')
            ->select('id', 'reason')
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->get();
        //  return $redeemtion ;
        $isRoleAbleToRejectRedemption = $this->isRoleAbleToRejectRedemption(\Auth::user()->role);

        return view('admin.redeemtion.edit', [
            'redeemtion'=> $redeemtion,
            'isRoleAbleToRejectRedemption' => $isRoleAbleToRejectRedemption,
            'orderPendingReasons' => $orderPendingReasons,
        ]) ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update_PREV(UpdateRedeemtionRequest $request, $id)
    // {
    //     \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.edit') ;
    //     $redeemtion = UserCatalogueRedeemtion::findOrFail($id) ;
    //     if($redeemtion->status != 0 && $redeemtion->status != 3)
    //     {
    //         Flash::error('Only pending or order placed requests can be process.');
    //         return redirect()->back() ;
    //     }
    //     $redeemtionDate = date('Y-m-d', strtotime($redeemtion->created_at)) ;
       
    //     if($request->status == 1 && $request->delivery_date < $redeemtionDate){
    //         Flash::error('Delivery date can not be before redeemption date.');
    //         return redirect()->back() ;
    //     }

    //     // If Rejected Then Credit the Debited  Point (Update The Net Point.)
    //     if($request->status == 2)
    //     {
    //        $rejectedRedeemtion = RejectedRedeemtion::where(['redeemtion_id'=> $redeemtion->id,  'user_id' => $redeemtion->user_id])->first() ;
           
    //        $data =  RejectedRedeemtion::updateOrcreate(['id'=> $rejectedRedeemtion->id ?? null], [
    //                     'redeemtion_id'  => $redeemtion->id,
    //                     'user_id'        => $redeemtion->user_id,
    //                     'point_credited' => $redeemtion->redeemed_point,
    //                     'description'    => "Redemtion Rejected on Order NO. ".$redeemtion->order_id,
    //                 ]);

    //        // Update User Net Point.
    //        $this->updatePoint($redeemtion->user_id);
    //     };
    //     $redeemtion->update($request->all()) ;

    //     if($request->status == UserCatalogueRedeemtion::STATUS_DELIVERED){
    //         $input['system_delivery_date_updated_at'] = now() ;
    //     }
        
    //     Flash::success('Updated successfully.');
    //     return redirect(route('redeemtions.index')) ;
    // }

    public function update(UpdateRedeemtionRequest $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.edit') ;
        
        try {

            if($request->status == UserCatalogueRedeemtion::STATUS_ORDER_PLACED && empty($request->order_tracking_url)){
                Flash::error('Order tracking URL is required. When order status is order placed.');
                return redirect()->back() ;
            }

            $redeemtion = UserCatalogueRedeemtion::findOrFail($id) ;
            $oldValues = $redeemtion->getOriginal();

            // if($redeemtion->status != 0 && $redeemtion->status != 3)
            if(!in_array($redeemtion->status, [UserCatalogueRedeemtion::STATUS_PENDING, UserCatalogueRedeemtion::STATUS_ORDER_PLACED]))
            {
                Flash::error('Only pending or order placed requests can be process.');
                return redirect()->back() ;
            }

            if($request->status == UserCatalogueRedeemtion::STATUS_REJECTED && !$this->isRoleAbleToRejectRedemption(\Auth::user()->role))
            {
                Flash::error('You are not allowed.');
                return redirect()->back() ;
            }

            $redeemtionDate = date('Y-m-d', strtotime($redeemtion->created_at)) ;
        
            if($request->status == 1 && $request->delivery_date < $redeemtionDate){
                Flash::error('Delivery date can not be before redeemption date.');
                return redirect()->back() ;
            }

            DB::beginTransaction();

            // If Rejected or Undelivered Then Credit the Debited  Point (Update The Net Point.)
            if(in_array($request->status, [UserCatalogueRedeemtion::STATUS_REJECTED, UserCatalogueRedeemtion::STATUS_UNDELIVERED]))
            {
            $rejectedRedeemtion = RejectedRedeemtion::where(['redeemtion_id'=> $redeemtion->id,  'user_id' => $redeemtion->user_id])->first() ;
            $rejectedRedeemtionDescription = "Redemtion Rejected on Order NO. ".$redeemtion->order_id;
            if($request->status == UserCatalogueRedeemtion::STATUS_UNDELIVERED)
            {
                $rejectedRedeemtionDescription = "Redemtion Undelivered on Order NO. ".$redeemtion->order_id;
            }
            $rejectedRedeemtionRow  =  RejectedRedeemtion::updateOrcreate(['id'=> $rejectedRedeemtion->id ?? null], [
                            'redeemtion_id'  => $redeemtion->id,
                            'user_id'        => $redeemtion->user_id,
                            'point_credited' => $redeemtion->redeemed_point,
                            'description'    => $rejectedRedeemtionDescription,
                        ]);

            // Update User Net Point.
            $this->updatePoint($redeemtion->user_id);
            };



            $input = $request->all();

            if($request->status == UserCatalogueRedeemtion::STATUS_DELIVERED){
                $input['system_delivery_date_updated_at'] = now() ;
            }

            // return $input ;
            $redeemtion->update($input) ;

            // ------ Push Notification ------

            $user      = User::find($redeemtion->user_id);
            $catalogue = Catalogue::find($redeemtion->catalogue_id);
            $body      =  $this->getNotificationMessage($request->status, $catalogue?->name, $redeemtion->order_id);

            if(!empty($user->fcm_token)){

                $allTitle = ['1' => 'Order Delivered', '2'=> 'Order Rejected', '3'=> 'Order Placed', '4' => 'Order Undelivered'] ;
                $title    =  $allTitle[$request->status] ?? 'Notification';
                $fcmData     = ['data'=> 'My Data'];

                $this->send_fcm_notification($user->fcm_token, $title, $body, $fcmData);
            }

            // ----------- To Send APP Notification -----------

        $notificationData = [
                "notification_type" => "Order Update",
                "data" => [
                    "msg" => $body. ' ' .Carbon::now(),
                ]
            ]; 
        Notification::send($user, new StarLinkNotification($notificationData));


           // ---------------- Log Entry. -----------------------------

            // Changed Values & Old Value History.
            $changedValues = $redeemtion->getChanges();

            $diff = [] ;

            foreach ($changedValues as $key => $item) {
                $diff[$key] = [
                'old_data' =>  $oldValues[$key],
                'new_data' =>  $item,
                ];
            }

            // Log Entry.
            $logData = [
                'table_id' => $redeemtion->id,
                'user_id' => \Auth::user()?->id,
                'model_name' => 'UserCatalogueRedeemtion',
                'request'=> json_encode($input) ,
                'response'=> json_encode($changedValues) ,
                'action' => 'update',
                'remarks'=> 'redeemtion edit',
                'data_updated' => json_encode($diff),
            ];

            $this->createLog($logData) ;

            DB::commit();

            // Send Mail Incase OF Status Rejected.
            if($request->status == UserCatalogueRedeemtion::STATUS_REJECTED)
            {
               //  [As Per Client Requirement Mail Part Is Commented Out Now 07/01/26].

                // $catelogue = Catalogue::find($redeemtion->catalogue_id) ;
                // $feedbackEmail = $this->settingVal("setting_name", "feedback_email");
                // $data['email'] = $feedbackEmail ;
                // $data['subject'] = 'Order Rejection of '.$redeemtion->order_id ;
                // $data['user'] = $user?->name ?? 'Not Found' ;
                // $data['product'] = $catelogue?->name; 
                // $data['remarks'] = $request->input('remarks'); 
                // $data['orderId'] = $redeemtion->order_id; 
                // $data['adminUser'] = \Auth::user()?->name." , ".date('d-m-Y h:i A'); 
                // if(!empty($feedbackEmail)){
                //     Mail::send('emails.rejected', $data, function ($message) use ($data) {
                //     $message->to($data['email'])
                //             ->subject($data['subject']);

                //     });
                // }
            }

            
            Flash::success('Updated successfully.');
            return redirect(route('redeemtions.index')) ;
        } 
        catch (\Exception $ex) {
            DB::rollback();
            Flash::error('Error: '. $ex->getMessage());
            return redirect(route('redeemtions.index')) ;

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
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.delete') ;
        //
    }
    
    public function export()
    {
        $loggedUser= \Auth::user();
        $query = '';
        if($loggedUser->role > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            $query = UserCatalogueRedeemtion::whereNotNull('catalogue_id')->with('catalogue')->whereHas('user.branch', function ($query) use($allocated_branches){
                $query->whereIn('id',$allocated_branches);
            })
            ->with(['user', 'user.branch.zone', 'user.by_created', 'user.te_linked', 'user.branch'])
            ->select([
                'user_catalogue_redeemtions.*'
            ]);
        }
        else
        {
            $query = UserCatalogueRedeemtion::whereNotNull('catalogue_id')->with('catalogue')
            ->with(['user', 'user.branch.zone', 'user.by_created', 'user.te_linked', 'user.branch'])
            ->select([
                'user_catalogue_redeemtions.*'
            ]);
        }
        $numberOfRecords = $query->count();
        set_time_limit(0);
        $filename = "Redemption_".$this->getUniqueId().".csv";
        $headings = [
            'Date',
            'Order No',
            'Contractor Name',
            'Contractor Phone',
            'BDE Code',
            'BDE Name',
            'Branch',
            'Zone',
            'Email',
            'Address1',
            'Address2',
            'City',
            'District',
            'State',
            'Country',
            'Pincode',
            'Catalogue',
            'Catalogue Type',
            'Status',
            'Delivery Confirmed',
            'Delivery Confirmed on',
            'Remarks',
            'Order Pending Reason',
            'Delivery Date',
            'System Delivery Date Updated At',
            'Catalogue Point',
            'TDS Percentage',
            'TDS Point',
            'Redeemed Point',
            'Feedback',
        ];

        $myfile = fopen(public_path("/excel_exports/redemption/").$filename, "w");
        fputcsv($myfile,$headings);
        $fetchDataLimit = 1000;
        $fetchDataFrom = 0;
        $i = 0;
        while($i < $numberOfRecords)
        {
            $data = $query->orderBy('id', 'DESC')->skip($fetchDataFrom)->take($fetchDataLimit)->get();
            foreach($data as $val)
            {
                $status = ['0'=> 'Pending','1' => 'Delivered', '2'=> 'Rejected', '3' => 'Order Placed'];
                $content = [
                    Carbon::createFromFormat('Y-m-d H:i:s', $val->created_at),
                    $val->order_id,
                    $val->user->name ?? "",
                    $val->user->phone ?? "",
                    $val->user->te_linked->emp_code ?? "",
                    $val->user->te_linked->name ?? "",
                    $val->user->branch->name ?? "",
                    $val->user->branch->zone->name ?? "",
                    (!empty($val->email) && $val->email != "null" && $val->catalogue->catalogue_type_id == 2) ? $val->email : "N/A",
                    $val->address1 != "null" ? $val->address1 : "",
                    $val->address2 != "null"  ? $val->address2 : "",
                    $val->city  != "null" ? $val->city : "",
                    $val->district  != "null" ? $val->district : "",
                    $val->state  != "null" ? $val->state : "",
                    $val->country  != "null" ? $val->country : "",
                    $val->pincode  != "null" ? $val->pincode : "",
                    $val->catalogue->name ?? "",
                    $val->catalogue->catalogueType->name ?? "",
                    //$status[$val->status] ?? "",
                    $val->getStatus(),
                    $val->getDeliveryConfirmationStatus(),
                    $val->delivery_confirmation_datetime,
                    $val->remarks,
                    $val->order_pending_reason,
                    isset($val->delivery_date) ? date('d-m-Y', strtotime($val->delivery_date)) : "",
                    isset($val->system_delivery_date_updated_at) ? date('d-m-Y', strtotime($val->system_delivery_date_updated_at)) : "",
                    $val->catalogue_point,
                    $val->catalogue_tds_percentage,
                    $val->catalogue_tds_point,
                    $val->redeemed_point,
                    $val->feedback,
                ];
                fputcsv($myfile,$content);
            }
            $fetchDataFrom += $fetchDataLimit;
            $i += $fetchDataLimit;
        }
        fclose($myfile);
        $filePath = public_path("/excel_exports/redemption/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    //-------Bulk Import------------

    public function showBulkUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.bulk-upload') ;
        if($request->session()->exists('redeemtion_import')){
            return view('admin.redeemtion.progress') ;
        }
        return view('admin.redeemtion.bulk-upload') ;
    }
    
    // public function uploadCsvFile_PREV(Request $request)
    // {
    //     \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.bulk-upload') ;
    //     set_time_limit(0);
    //     try 
    //     {
    //         if($request->hasFile('csvFile'))
    //         {
    //             $file       = $request->file('csvFile');
    //             $folderPath = \Storage::disk('public')->put('temp', $file);
    //             $actualPath = storage_path($folderPath);
    //             $fileWithPath =  \Storage::disk('public')->path($folderPath) ;
    //             $records = array_map('str_getcsv', file($fileWithPath));
    //             $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
    //             $count = 0; $unProcessedCount = 0;
    //             session()->put('redeemtion_import', $fileWithPath) ;
    //             session()->put('redeemtion_count', 0) ;
    //             $unprocessedData=[];
    //             $i=1;
    //             $importingFile = fopen($fileWithPath, 'r');
    //             // $headers = fgetcsv($importingFile);
    //             $row = [];
    //             $rowCount = 0;
    //             while (($rowLine = fgetcsv($importingFile)) !== false) 
    //             {
    //                     if($rowCount > 0)
    //                     {
    //                         $row = array_map(function ($value) 
    //                         {
    //                             return str_replace(["\r", "\n"], ' ', $value);
    //                         }, $rowLine);
    //                         if(!empty($row[0]) || !empty($row[1]))
    //                         {
    //                             if(empty($row[0]))
    //                             {
    //                                 array_push($unprocessedData,"<br>In row ".$i.", order ID is required. ");
    //                                 $unProcessedCount++ ;
    //                                 continue;
    //                             }
    //                             if(empty($row[1]))
    //                             {
    //                                 array_push($unprocessedData,"<br>In row ".$i.", order status is required. ");
    //                                 $unProcessedCount++ ;
    //                                 continue;
    //                             }
    //                             $redeemtionRecord = UserCatalogueRedeemtion::where('order_id', $row[0])->first();
    //                             if(empty($redeemtionRecord))
    //                             {
    //                                 array_push($unprocessedData,"<br>In row ".$i.", invalid order ID.");
    //                                 $unProcessedCount++ ;
    //                                 continue;
    //                             }
    //                             if($redeemtionRecord->status != 0 && $redeemtionRecord->status != 3)
    //                             {
    //                                 array_push($unprocessedData,"<br>In row ".$i.", order should be in pending or order placed state to process.");
    //                                 $unProcessedCount++ ;
    //                                 continue;
    //                             }
    //                             if($row[1] != 2)
    //                             {
    //                                 array_push($unprocessedData,"<br>In row ".$i.", only order status code 2 is accepted which is rejected.");
    //                                 $unProcessedCount++ ;
    //                                 continue;
    //                             }
    //                             //Credit the Debited  Point (Update The Net Point.)
    //                             $rejectedRedeemtionRecord = RejectedRedeemtion::where(['redeemtion_id'=> $redeemtionRecord->id,  'user_id' => $redeemtionRecord->user_id])->first() ;
           
    //                             RejectedRedeemtion::updateOrcreate(['id'=> $rejectedRedeemtionRecord->id ?? null], [
    //                                             'redeemtion_id'  => $redeemtionRecord->id,
    //                                             'user_id'        => $redeemtionRecord->user_id,
    //                                             'point_credited' => $redeemtionRecord->redeemed_point,
    //                                             'description'    => "Redemtion Rejected on Order NO. ".$redeemtionRecord->order_id,
    //                                             'remarks' => $row[2] ?? "",
    //                                         ]);

    //                             // Update User Net Point.
    //                             $this->updatePoint($redeemtionRecord->user_id);
    //                             // Update redeemtion status.
    //                             $redeemtionRecord->update([
    //                                 'status' => $row[1],
    //                                 'remarks' => $row[2] ?? "",
    //                             ]) ;

    //                             $count++ ;
    //                             session()->put('redeemtion_count', $count) ;
                                
    //                             // $request->session()->save();
    //                             // sleep(10) ;
                               
    //                           //  echo session()->get('total_count');
                               
                            
    //                         }
                           
                        
    //                     }
    //                     if($rowCount == $lineSllice){
    //                         $request->session()->save();
    //                         // sleep(1) ;
    //                     }
    //                     $i++;
    //                     $rowCount++;
    //             }
    //             fclose($importingFile);
    //             $request->session()->forget('redeemtion_import');
    //             unlink($fileWithPath);
                 
    //             return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('redeemtion_count').' records processed. & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200); 
    
    //         }
    
            
    //     } catch (\Exception $e) {
    //         $request->session()->forget('redeemtion_import');
    //         return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
    //     }
                

       
    // }

    
    /*public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.bulk-upload') ;
        set_time_limit(0);
        try 
        {
            if($request->hasFile('csvFile'))
            {
               // 0= Pending, 1 = delivered, 2 = rejected, 3 = order placed, 4 = undelivered, 5 = delivery acknowledgement, 6 = complaint feedback.

                $allowedStatus            = ['1', '2', '3', '4', '5'];
                $statusBeforeDelivered    = ['3'];
                $statusBeforeRejected     = ['0', '1', '3'];
                $statusBeforeOrderPlaced  = ['0'];
                $statusBeforeUndelivered  = ['1', '3', '5', '6'];

                $file       = $request->file('csvFile');
                $folderPath = \Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  \Storage::disk('public')->path($folderPath) ;
                $records      = array_map('str_getcsv', file($fileWithPath));
                $lineSllice   = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = 0; $unProcessedCount = 0;
                session()->put('redeemtion_import', $fileWithPath) ;
                session()->put('redeemtion_count', 0) ;
                $unprocessedData=[];
                $i=0;
                $importingFile = fopen($fileWithPath, 'r');
                // $headers = fgetcsv($importingFile);
                $row = [];
                $rowCount = 0;

                DB::beginTransaction();
                while (($rowLine = fgetcsv($importingFile)) !== false) 
                {
                        $i++;
                        
                        if($rowCount > 0)
                        {
                            $row = array_map(function ($value) 
                            {
                                return str_replace(["\r", "\n"], ' ', $value);
                            }, $rowLine);
                            if(!empty($row[0]) || !empty($row[1]))
                            {
                                if(empty($row[0]))
                                {
                                    array_push($unprocessedData,"<br>In row ".$i.", order ID is required. ");
                                    $unProcessedCount++ ;
                                    continue;
                                }
                                if(empty($row[1]))
                                {
                                    array_push($unprocessedData,"<br>In row ".$i.", order status is required. ");
                                    $unProcessedCount++ ;
                                    continue;
                                }

                                if(!in_array($row[1], $allowedStatus))
                                {
                                    array_push($unprocessedData,"<br>In row ".$i.", Only order status delivered, order placed, Rejected are allowed only. ");
                                    $unProcessedCount++ ;
                                    continue;
                                }

                               

                                $redeemtionRecord = UserCatalogueRedeemtion::where('order_id', $row[0])->first();
                                $oldValues        = $redeemtionRecord->getOriginal();

                                if(empty($redeemtionRecord))
                                {
                                    array_push($unprocessedData,"<br>In row ".$i.", invalid order ID.");
                                    $unProcessedCount++ ;
                                    continue;
                                }
                                
                                // To Change The Status delivered.
                                if($row[1] == 1 && !in_array($redeemtionRecord->status, $statusBeforeDelivered)){
                                       array_push($unprocessedData,"<br>In row ".$i.", order should be order placed state to change the status to deivered.");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                if($row[1] == 1 && empty($row[5])){
                                       array_push($unprocessedData,"<br>In row ".$i.", Deivery date is required when status is delivered");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                // To Change The Status Order Placed.
                                if($row[1] == 3 && !in_array($redeemtionRecord->status, $statusBeforeOrderPlaced)){
                                    
                                       array_push($unprocessedData,"<br>In row ".$i.", order should be in pending state to chage the status to order placed.");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                if($row[1] == 3 && (empty(trim($row[3])) || empty(trim($row[4])))){
                                       array_push($unprocessedData,"<br>In row ".$i.", Order tracking url & order tracking id is required when status is order placed");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                // To Change The Status Order Rejected.
                                if($row[1] == 2 && !in_array($redeemtionRecord->status, $statusBeforeRejected)){
                                       array_push($unprocessedData,"<br>In row ".$i.", order should be in pending state or order placed state to chage the status to rejected.");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                // Order Status Rejected & Remarks Is Empty.
                                if($row[1] == 2 && (empty(trim($row[2])))){
                                       array_push($unprocessedData,"<br>In row ".$i.", remarks is required when status is rejected.");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                // To Change The Status Undelivered.
                                if($row[1] == 4 && !in_array($redeemtionRecord->status, $statusBeforeUndelivered)){
                                       array_push($unprocessedData,"<br>In row ".$i.", Redeemption order status should be in delivered or delivery acknowledgement or complain/feedback to chage it undelivered.");
                                       $unProcessedCount++ ;
                                       continue;
                                }
                                

                                //Credit the Debited  Point (Update The Net Point.)
                                if($row[1] == 2 || $row[1] == 4){
           
                                    $input = [
                                        'status'=> $row[1],
                                        'remarks'=> $row[2],
                                    ];
                                    $rejectedRedeemtionRecord = RejectedRedeemtion::where(['redeemtion_id'=> $redeemtionRecord->id,  'user_id' => $redeemtionRecord->user_id])->first() ;
                                    RejectedRedeemtion::updateOrcreate(['id'=> $rejectedRedeemtionRecord->id ?? null], [
                                                    'redeemtion_id'  => $redeemtionRecord->id,
                                                    'user_id'        => $redeemtionRecord->user_id,
                                                    'point_credited' => $redeemtionRecord->redeemed_point,
                                                    'description'    => "Redemtion Rejected on Order NO. ".$redeemtionRecord->order_id,
                                                    'remarks'        => $row[2] ?? "",
                                    ]);

                                    // Update User Net Point.
                                    $this->updatePoint($redeemtionRecord->user_id);
                                }
                               

                                
                               
                                if($row[1] == 3){ // Order Placed.
                                    $input = [
                                        'status' => $row[1],
                                        'order_tracking_url' => $row[3] ?? "",
                                        'order_tracking_id'  => $row[4] ?? "",
                                       
                                    ];
                                }
                                if($row[1] == 1){ // Order Delivered.
                                   $input = [
                                        'status' => $row[1],
                                        'delivery_date' => date('Y-m-d', strtotime($row[5])) ?? "",
                                        'system_delivery_date_updated_at' => now() ,
                                    ]; 

                                    
                                }

                                if($row[1] == 4){ // Order Undelivered.
                                   $input = [
                                        'status'   => $row[1],
                                        'remarks'  => $row[2] ?? "",
                                    ]; 
                                }

                                 // Update redeemtion status.
                                $redeemtionRecord->update($input) ;

                                 // ------ Push Notification ------

                                $user      = User::find($redeemtionRecord->user_id);
                                $catalogue = Catalogue::find($redeemtionRecord->catalogue_id);
                                $body      =  $this->getNotificationMessage($request->status, $catalogue?->name, $redeemtionRecord->order_id);

                                if(!empty($user->fcm_token)){

                                    $allTitle = ['1' => 'Order Delivered', '2'=> 'Order Rejected', '3'=> 'Order Placed', '4' => 'Order Undelivered'] ;
                                    $title    =  $allTitle[$row[1]] ?? 'Notification';
                                    $data     = ['data'=> 'My Data'];

                                    $this->send_fcm_notification($user->fcm_token, $title, $body, $data);
                                }

                            // ----------- To Send APP Notification -----------

                            $notificationData = [
                                    "notification_type" => "Order Update",
                                    "data" => [
                                        "msg" => $body. ' ' .Carbon::now(),
                                    ]
                                ]; 

                            Notification::send($user, new StarLinkNotification($notificationData));


                            // ---------------- Log Entry. -----------------------------

                                // Changed Values & Old Value History.
                                $changedValues = $redeemtionRecord->getChanges();

                                $diff = [] ;

                                foreach ($changedValues as $key => $item) {
                                    $diff[$key] = [
                                    'old_data' =>  $oldValues[$key],
                                    'new_data' =>  $item,
                                    ];
                                }

                                // Log Entry.
                                $logData = [
                                    'table_id' => $redeemtionRecord->id,
                                    'user_id' => \Auth::user()?->id,
                                    'model_name' => 'UserCatalogueRedeemtion',
                                    'request'=> json_encode($input) ,
                                    'response'=> json_encode($changedValues) ,
                                    'action' => 'update',
                                    'remarks'=> 'redeemtion edit by bulk upload',
                                    'data_updated' => json_encode($diff),
                                ];

                                $this->createLog($logData) ;



                                $count++ ;
                                session()->put('redeemtion_count', $count) ;
                                
                                // $request->session()->save();
                                // sleep(10) ;
                               
                              //  echo session()->get('total_count');
                               
                            
                            }
                           
                        
                        }
                        if($rowCount == $lineSllice){
                            $request->session()->save();
                            // sleep(1) ;
                        }
                       
                        $rowCount++;
                }
                fclose($importingFile);
                $request->session()->forget('redeemtion_import');
                unlink($fileWithPath);
                DB::commit();
                 
                return response()->json(['success'=> true, '$rowCount'=> $rowCount, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('redeemtion_count').' records processed. & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200); 
    
            }
    
            
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->forget('redeemtion_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }*/
        public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.bulk-upload') ;
        set_time_limit(0);
        try 
        {
            if($request->hasFile('csvFile'))
            {
               // 0= Pending, 1 = delivered, 2 = rejected, 3 = order placed, 4 = undelivered, 5 = delivery acknowledgement, 6 = complaint feedback.

                $allowedStatus            = ['0','1', '2', '3', '4', '5'];
                $statusBeforeDelivered    = ['3'];
                $statusBeforeRejected     = ['0', '1', '3'];
                $statusBeforeOrderPlaced  = ['0'];
                $statusBeforeUndelivered  = ['1', '3', '5', '6'];

                $file       = $request->file('csvFile');
                $folderPath = \Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  \Storage::disk('public')->path($folderPath) ;
                $records      = array_map('str_getcsv', file($fileWithPath));
                $lineSllice   = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = 0; $unProcessedCount = 0;
                session()->put('redeemtion_import', $fileWithPath) ;
                session()->put('redeemtion_count', 0) ;
                $unprocessedData=[];
                $i=0;
                $importingFile = fopen($fileWithPath, 'r');
                // $headers = fgetcsv($importingFile);
                $row = [];
                $rowCount = 0;

                DB::beginTransaction();
                while (($rowLine = fgetcsv($importingFile)) !== false) 
                {
                        $i++;
                        
                        if($rowCount > 0)
                        {
                            $row = array_map(function ($value) 
                            {
                                return str_replace(["\r", "\n"], ' ', $value);
                            }, $rowLine);
                            if(!empty($row[0]) || !empty($row[1]))
                            {
                                if(empty($row[0]))
                                {
                                    array_push($unprocessedData,"<br>In row ".$i.", order ID is required. ");
                                    $unProcessedCount++ ;
                                    continue;
                                }
                               /* if(empty($row[1]))
                                {
                                    array_push($unprocessedData,"<br>In row ".$i.", order status is required. ");
                                    $unProcessedCount++ ;
                                    continue;
                                }*/
                                if(!isset($row[1]) || trim($row[1]) === '')
                                    {
                                        array_push($unprocessedData,"<br>In row ".$i.", order status is required. ");
                                        $unProcessedCount++ ;
                                        continue;
                                    }

                                if(!in_array($row[1], $allowedStatus))
                                {
                                    array_push($unprocessedData,"<br>In row ".$i.", Only order status delivered, order placed, Rejected are allowed only. ");
                                    $unProcessedCount++ ;
                                    continue;
                                }

                               

                                $redeemtionRecord = UserCatalogueRedeemtion::where('order_id', $row[0])->first();
                                $oldValues        = $redeemtionRecord->getOriginal();

                                if(empty($redeemtionRecord))
                                {
                                    array_push($unprocessedData,"<br>In row ".$i.", invalid order ID.");
                                    $unProcessedCount++ ;
                                    continue;
                                }
                                
                                // To Change The Status delivered.
                                if($row[1] == 1 && !in_array($redeemtionRecord->status, $statusBeforeDelivered)){
                                       array_push($unprocessedData,"<br>In row ".$i.", order should be order placed state to change the status to deivered.");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                if($row[1] == 1 && empty($row[5])){
                                       array_push($unprocessedData,"<br>In row ".$i.", Deivery date is required when status is delivered");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                // To Change The Status Order Placed.
                                if($row[1] == 3 && !in_array($redeemtionRecord->status, $statusBeforeOrderPlaced)){
                                    
                                       array_push($unprocessedData,"<br>In row ".$i.", order should be in pending state to chage the status to order placed.");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                if($row[1] == 3 && (empty(trim($row[3])) || empty(trim($row[4])))){
                                       array_push($unprocessedData,"<br>In row ".$i.", Order tracking url & order tracking id is required when status is order placed");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                // To Change The Status Order Rejected.
                                if($row[1] == 2 && !in_array($redeemtionRecord->status, $statusBeforeRejected)){
                                       array_push($unprocessedData,"<br>In row ".$i.", order should be in pending state or order placed state to chage the status to rejected.");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                // Order Status Rejected & Remarks Is Empty.
                                if($row[1] == 2 && (empty(trim($row[2])))){
                                       array_push($unprocessedData,"<br>In row ".$i.", remarks is required when status is rejected.");
                                       $unProcessedCount++ ;
                                       continue;
                                }

                                // To Change The Status Undelivered.
                                if($row[1] == 4 && !in_array($redeemtionRecord->status, $statusBeforeUndelivered)){
                                       array_push($unprocessedData,"<br>In row ".$i.", Redeemption order status should be in delivered or delivery acknowledgement or complain/feedback to chage it undelivered.");
                                       $unProcessedCount++ ;
                                       continue;
                                }
                                // To Change The Status Pending.
                                    if($row[1] == 0){
                                        $orderPendingReason = DB::table('order_pending_reason')
                                            ->where('id', $row[2])
                                            ->where('status', 1)
                                            ->value('reason');

                                        $input = [
                                            'status'  => $row[1],
                                            
                                        ];
                                        if(empty($orderPendingReason)){
                                        $input = [
                                            
                                            'remarks' => $row[2] ?? "",
                                        ];
                                        }
                                        if(!empty($orderPendingReason)){
                                            $input['order_pending_reason'] = $orderPendingReason;
                                            $input['remarks'] = '';
                                        }
                                    }

                                //Credit the Debited  Point (Update The Net Point.)
                                if($row[1] == 2 || $row[1] == 4){
           
                                    $input = [
                                        'status'=> $row[1],
                                        'remarks'=> $row[2],
                                    ];
                                    $rejectedRedeemtionRecord = RejectedRedeemtion::where(['redeemtion_id'=> $redeemtionRecord->id,  'user_id' => $redeemtionRecord->user_id])->first() ;
                                    RejectedRedeemtion::updateOrcreate(['id'=> $rejectedRedeemtionRecord->id ?? null], [
                                                    'redeemtion_id'  => $redeemtionRecord->id,
                                                    'user_id'        => $redeemtionRecord->user_id,
                                                    'point_credited' => $redeemtionRecord->redeemed_point,
                                                    'description'    => "Redemtion Rejected on Order NO. ".$redeemtionRecord->order_id,
                                                    'remarks'        => $row[2] ?? "",
                                    ]);

                                    // Update User Net Point.
                                    $this->updatePoint($redeemtionRecord->user_id);
                                }
                               

                                
                               
                                if($row[1] == 3){ // Order Placed.
                                    $input = [
                                        'status' => $row[1],
                                        'order_tracking_url' => $row[3] ?? "",
                                        'order_tracking_id'  => $row[4] ?? "",
                                       
                                    ];
                                }
                                if($row[1] == 1){ // Order Delivered.
                                   $input = [
                                        'status' => $row[1],
                                        'delivery_date' => date('Y-m-d', strtotime($row[5])) ?? "",
                                        'system_delivery_date_updated_at' => now() ,
                                    ]; 

                                    
                                }

                                if($row[1] == 4){ // Order Undelivered.
                                   $input = [
                                        'status'   => $row[1],
                                        'remarks'  => $row[2] ?? "",
                                    ]; 
                                }

                                 // Update redeemtion status.
                                $redeemtionRecord->update($input) ;

                                 // ------ Push Notification ------

                                $user      = User::find($redeemtionRecord->user_id);
                                $catalogue = Catalogue::find($redeemtionRecord->catalogue_id);
                                $body      =  $this->getNotificationMessage($request->status, $catalogue?->name, $redeemtionRecord->order_id);

                                if(!empty($user->fcm_token)){

                                    $allTitle = [ '0' => 'Order Pending', '1' => 'Order Delivered', '2'=> 'Order Rejected', '3'=> 'Order Placed', '4' => 'Order Undelivered'] ;
                                    $title    =  $allTitle[$row[1]] ?? 'Notification';
                                    $data     = ['data'=> 'My Data'];

                                    $this->send_fcm_notification($user->fcm_token, $title, $body, $data);
                                }

                            // ----------- To Send APP Notification -----------

                            $notificationData = [
                                    "notification_type" => "Order Update",
                                    "data" => [
                                        "msg" => $body. ' ' .Carbon::now(),
                                    ]
                                ]; 

                            Notification::send($user, new StarLinkNotification($notificationData));


                            // ---------------- Log Entry. -----------------------------

                                // Changed Values & Old Value History.
                                $changedValues = $redeemtionRecord->getChanges();

                                $diff = [] ;

                                foreach ($changedValues as $key => $item) {
                                    $diff[$key] = [
                                    'old_data' =>  $oldValues[$key],
                                    'new_data' =>  $item,
                                    ];
                                }

                                // Log Entry.
                                $logData = [
                                    'table_id' => $redeemtionRecord->id,
                                    'user_id' => \Auth::user()?->id,
                                    'model_name' => 'UserCatalogueRedeemtion',
                                    'request'=> json_encode($input) ,
                                    'response'=> json_encode($changedValues) ,
                                    'action' => 'update',
                                    'remarks'=> 'redeemtion edit by bulk upload',
                                    'data_updated' => json_encode($diff),
                                ];

                                $this->createLog($logData) ;



                                $count++ ;
                                session()->put('redeemtion_count', $count) ;
                                
                                // $request->session()->save();
                                // sleep(10) ;
                               
                              //  echo session()->get('total_count');
                               
                            
                            }
                           
                        
                        }
                        if($rowCount == $lineSllice){
                            $request->session()->save();
                            // sleep(1) ;
                        }
                       
                        $rowCount++;
                }
                fclose($importingFile);
                $request->session()->forget('redeemtion_import');
                unlink($fileWithPath);
                DB::commit();
                 
                return response()->json(['success'=> true, '$rowCount'=> $rowCount, 'import_status'=> 1, 'message'=> 'Import Successfull '.session()->get('redeemtion_count').' records processed. & '.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData)], 200); 
    
            }
    
            
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->forget('redeemtion_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200); ;
    
           
        }
                

       
    }
    
    public function getProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('redeemtions.bulk-upload') ;
        if($request->session()->has('redeemtion_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('redeemtion_count'), 'message'=> 'Importing Data. Please wait....'], 200); ;

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('redeemtion_count').' records procxessed.'], 200);

    }
}
