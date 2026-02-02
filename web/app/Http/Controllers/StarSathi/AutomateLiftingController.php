<?php

namespace App\Http\Controllers\StarSathi;

use App\Models\Setting;
use App\Models\Lifting;
use App\Models\LiftingApprovalHistory;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Exception;
use App\Models\Log;
use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Support\Facades\DB;
use App\Traits\HelperTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StarLinkNotification;
use Illuminate\Support\Facades\Http;

class AutomateLiftingController extends Controller
{
    use HelperTrait;

    function automateExportLedger()
    {
        try
        {
            set_time_limit(0);
            $limit = 1000;
            $offset = 0;
            $count = 0;
            $filename = "Point_Ledger.csv";
            $headings = [
                "Lifting Date",
                "Creation Date",
                "Order No",
                "Name",
                "Phone No.",
                "Branch",
                "BDE Code",
                "BDE Name",
                "Description",
                "Credit Point",
                "Debit Point",
            ];

            $myfile = fopen(public_path("/excel_exports/automate_ledger_points/").$filename, "w");
            fputcsv($myfile,$headings);
            while(true)
            {
                    
                // $data = DB::select("SELECT * FROM (
                //         SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`
                //         FROM `user_catalogue_redeemtions`
                //         UNION ALL
                //         SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point` AS `credit_point`,'',`description`,`created_at`
                //         FROM `rewards` WHERE `is_verified`='1'
                //         UNION ALL
                //         SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`
                //         FROM `rejected_redeemtions`
                //         )P
                //         ORDER BY `created_at` LIMIT ?  OFFSET ?", [$limit, $offset]
                // );

                $data = DB::select("SELECT * FROM (
                        SELECT get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
                        created_at AS ledger_date, NULL AS `lifting_date` FROM `user_catalogue_redeemtions`
                        UNION ALL
                        SELECT get_mason_details(`rewards`.`user_id`) AS mason_details,'',`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
                        rewards.updated_at AS ledger_date, `lifting`.`lifting_date` FROM `rewards` LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `rewards`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
                        UNION ALL
                        SELECT get_mason_details(`reward_history`.`user_id`) AS mason_details,'',`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
                        reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date` FROM `reward_history` LEFT JOIN rewards ON rewards.id = reward_history.reward_id LEFT JOIN lifting ON rewards.lifting_id = lifting.id WHERE `reward_history`.`is_eligible_for_ledger`='".(RewardHistory::ELIGIBLE_FOR_LEDGER_YES)."'
                        UNION ALL
                        SELECT get_mason_details(`user_id`) AS mason_details,'',`user_id`, `point_credited` AS `credit_point`,'',`description`,`created_at`,
                        created_at AS ledger_date, NULL AS `lifting_date` FROM `rejected_redeemtions`
                        )P
                        ORDER BY ledger_date LIMIT ?  OFFSET ?", [$limit, $offset]);

                foreach($data as $val)
                {
                    $mason_details = json_decode($val->mason_details);
                    $content = [
                        !empty($val->lifting_date) ? Carbon::parse($val->lifting_date)->toDateString() : "N/A",
                        Carbon::parse($val->created_at)->toDateString(),
                        $val->order_id,
                        $mason_details == null ? "" : $mason_details->name,
                        $mason_details == null ? "" : $mason_details->phone,
                        $mason_details == null ? "" : ($mason_details->branch == null ? "" : $mason_details->branch->name),
                        $mason_details == null ? "" : ($mason_details->te == null ? "" : $mason_details->te->code),
                        $mason_details == null ? "" : ($mason_details->te == null ? "" : $mason_details->te->name),
                        $val->description,
                        $val->credit_point,
                        $val->debit_point,
                    ];
                    fputcsv($myfile,$content);
                }
                if(count($data) < $limit)
                {
                    $count += count($data);
                    break;
                }
                $count += $limit;
                $offset += $limit;
            }
            fclose($myfile);
        }
        catch (Exception $e) {
            LaravelLog::info('Error Occured in Automate Ledger Export. --> '.$e->getMessage());
        }
        LaravelLog::info('Automate Ledger Export Cron Executed');
    }

    // function automateVerifyLiftingReport()
    // {
    //     LaravelLog::info('Automate Lifting Report Cron Execution started');
    //     set_time_limit(0);
    //     try{
    //         // Path where to store CSVs
    //         $path = public_path("/excel_exports/automate_verify_liftings");
    //         $months = [];
    //         //Current Month
    //         array_push($months, [
    //             "fromDate" => Carbon::now()->startOfMonth()->toDateString(),
    //             "toDate" => Carbon::now()->endOfMonth()->toDateString(),
    //             "fileName" => Carbon::now()->endOfMonth()->format("M-Y").".csv",
    //             "tempFileName" => "Temp-".Carbon::now()->endOfMonth()->format("M-Y").".csv",
    //         ]);
    //         //Previous Month
    //         array_push($months, [
    //             "fromDate" => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
    //             "toDate" => Carbon::now()->subMonth()->endOfMonth()->toDateString(),
    //             "fileName" => Carbon::now()->subMonth()->endOfMonth()->format("M-Y").".csv",
    //             "tempFileName" => "Temp-".Carbon::now()->subMonth()->endOfMonth()->format("M-Y").".csv",
    //         ]);
    //         //2 Month Ago
    //         array_push($months, [
    //             "fromDate" => Carbon::now()->subMonths(2)->startOfMonth()->toDateString(),
    //             "toDate" => Carbon::now()->subMonth(2)->endOfMonth()->toDateString(),
    //             "fileName" => Carbon::now()->subMonth(2)->endOfMonth()->format("M-Y").".csv",
    //             "tempFileName" => "Temp-".Carbon::now()->subMonth(2)->endOfMonth()->format("M-Y").".csv",
    //         ]);

    //         // Make sure the export folder exists
    //         if (!file_exists($path)) {
    //             mkdir($path, 0755, true);
    //         }
    //         foreach($months as $month)
    //         {
    //             $fromDate = $month["fromDate"];
    //             $toDate = $month["toDate"];
    //             $query = Lifting::with('product')->with('mason_user')->with('user')->with('reward')
    //                     ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$fromDate, $toDate]);
    //             $numberOfRecords = $query->count();
    //             set_time_limit(0);
    //             // $filename = "Verify_Liftings_".$this->getUniqueId().".csv";
    //             $headings = [
    //                 'Lifting ID',
    //                 'Date',
    //                 'Dealer',
    //                 'Dealer Code',
    //                 'Dealer SAP Code',
    //                 'Mason',
    //                 'Mason Mobile',
    //                 'Mason Branch',
    //                 'TE Code',
    //                 'TE Name',
    //                 'TE Phone',
    //                 'Zone',
    //                 'Product Name',
    //                 'Approved Quantity',
    //                 'Mason Submitted Quantity',
    //                 'Dealer Edited Quantity',
    //                 'BD Edited Quantity',
    //                 'Last Modified By',
    //                 'Last Modified Date and Time',
    //                 'Auto Approved',
    //                 'Lifting By',
    //                 'Lifting Creation Date and Time',
    //                 'Point',
    //                 'Attachment',
    //                 'Status',
    //                 'Star Saathi / ASM Status',
    //                 'Action Taken At',
    //                 'verified_by',
    //                 'verified_at',
    //             ];
                

    //             $tempFilePath = $path . '/' . $month["tempFileName"];
    //             $finalFilePath = $path . '/' . $month["fileName"];

    //             $myfile = fopen($tempFilePath, "w");
    //             // Try to get a non-blocking exclusive lock
    //             if (flock($myfile, LOCK_EX | LOCK_NB)) 
    //             {
    //                 fputcsv($myfile,$headings);
    //                 $fetchDataLimit = 1000;
    //                 $fetchDataFrom = 0;
    //                 $i = 0;
    //                 $dataProcessedCount = 0;
    //                 while($i < $numberOfRecords)
    //                 {
    //                     $data = $query->skip($fetchDataFrom)->take($fetchDataLimit)->get();
    //                     foreach($data as $lifting)
    //                     {
    //                         $rewards = $lifting->reward ?? [] ;
    //                         $totalPoint = 0 ;
    //                         foreach ($rewards as $key => $row) {
    //                             $totalPoint += $row['point'] ;
    //                         }
    //                         $starSaathiStatus = '';
    //                         $masonSubmitedQty = '';
    //                         $dealerEditedQty = '';
    //                         $bdEditedQty = '';
    //                         $lastModifiedBy = '';
    //                         $lastModifiedDateTime = '';
    //                         $isLiftingAsmOrStarSaathiQuery = LiftingApprovalHistory::where(["lifting_id" => $lifting->id]);
    //                         $isLiftingAsmOrStarSaathi = $isLiftingAsmOrStarSaathiQuery->get();
    //                         $asm = null;
    //                         foreach($isLiftingAsmOrStarSaathi as $val)
    //                         {
    //                             if($val->action_status == 0 && $val->seek_approval == 4)
    //                             {
    //                                 $asm = $val;
    //                             }
    //                         }
    //                         // $asm = LiftingApprovalHistory::where(['lifting_id' => $lifting->id, "action_status" => 0, "seek_approval" => 4])->orderBy("id", "DESC")->first();
    //                         $lifting_by = "";
    //                         if($lifting->req_type == 1)
    //                         {
    //                             $lifting_by = "OTP";
    //                         }
    //                         if($lifting->req_type == 2)
    //                         {
    //                             $lifting_by = "Star Saathi";
    //                         }
    //                         if($asm != null)
    //                         {
    //                             $lifting_by = "ASM - ". (User::find($asm->seek_approval_by)->name ?? "");
    //                         }
    //                         $countOfIsLiftingAsmOrStarSaathi = count($isLiftingAsmOrStarSaathi);
    //                         if($countOfIsLiftingAsmOrStarSaathi > 0)
    //                         {
    //                             if($lifting->req_status == 0)
    //                             {
    //                                 $starSaathiStatus = 'Pending';
    //                             }
    //                             else if($lifting->req_status == 1)
    //                             {
    //                                 $starSaathiStatus = 'Approved';
    //                             }
    //                             else
    //                             {
    //                                 $starSaathiStatus = 'Rejected';
    //                             }
    //                             if($lifting->req_type == 2)
    //                             {
    //                                 foreach($isLiftingAsmOrStarSaathi as $val)
    //                                 {
    //                                     if($val->action_status == 0)
    //                                     {
    //                                         if($val->seek_approval == 4)
    //                                         {
    //                                             $lifting_by = "ASM - ". (User::find($val->seek_approval_by)->name ?? "");
    //                                         }
    //                                         $masonSubmitedQty = $val->qty;
    //                                     }
    //                                     else if($val->action_status == 1)
    //                                     {
    //                                         $user = User::find($val->action_taken_by);
    //                                         $lastModifiedRecord = $val;
    //                                         if($user->role == 1)
    //                                         {
    //                                             $bdEditedQty = $val->qty;
    //                                         }
    //                                         else if(in_array($user->role, [3,4,6]))
    //                                         {
    //                                             $dealerEditedQty = $val->qty;
    //                                         }
    //                                     }
    //                                     else if($val->action_status == 3)
    //                                     {

    //                                     }
    //                                 }
    //                                 $lastModifiedBy = $user->roles->role_name ?? "";
    //                                 $lastModifiedDateTime = $lastModifiedRecord->created_at ?? "";
    //                             }
    //                         }
    //                         $attachment = $lifting->reward[0]->attachment ?? "";
    //                         if($attachment == null)
    //                         {
    //                             $attachment = null;
    //                         }
    //                         else
    //                         {
    //                             $attachment = asset($attachment);
    //                         }
    //                         $autoLiftingApproval = "No";
    //                         if($countOfIsLiftingAsmOrStarSaathi > 0 && $lifting->seek_approval == 3)
    //                         {
    //                             $autoLiftingApproval = "Yes";
    //                         }
    //                         $userNames = [];
    //                         $verified_by_ids = $lifting->reward[0]->verified_by_history ?? false;
    //                         $verified_by_ids = $verified_by_ids ? json_decode($verified_by_ids) : [];
    //                         foreach($verified_by_ids as $verified_by_id)
    //                         {
    //                             if($verified_by_id == 0)
    //                             {
    //                                 array_push($userNames, "Import");
    //                             }
    //                             else
    //                             {
    //                                 array_push($userNames, User::find($verified_by_id)->name ?? "");
    //                             }
    //                         }
    //                         $verified_by_at = $lifting->reward[0]->verified_by_at_history ?? false ;
    //                         $content = [
    //                             "LF".str_pad($lifting->id,10,"0",STR_PAD_LEFT ),
    //                             $lifting->lifting_date ?? "",
    //                             $lifting->user->name ?? "",
    //                             $lifting->user->emp_code ?? "",
    //                             $lifting->user->sap_code ?? "",
    //                             $lifting->mason_user->user->name ?? "",
    //                             $lifting->mason_user->user->phone ?? "",
    //                             $lifting->mason_user->user->branch->name ?? "",
    //                             $lifting->mason_user->user->te_linked->emp_code ?? "",
    //                             $lifting->mason_user->user->te_linked->name ?? "",
    //                             $lifting->mason_user->user->te_linked->phone ?? "",
    //                             $lifting->mason_user->user->branch->zone->name ?? "",
    //                             $lifting->product->name ?? "",
    //                             $lifting->qty ?? "",
    //                             $masonSubmitedQty,
    //                             $dealerEditedQty,
    //                             $bdEditedQty,
    //                             $lastModifiedBy,
    //                             $lastModifiedDateTime,
    //                             $autoLiftingApproval,
    //                             $lifting_by,
    //                             $lifting->created_at ?? "",
    //                             $totalPoint ?? "",
    //                             $attachment,
    //                             ($lifting->reward[0]->is_verified ?? "") == 1 ? 'Verified' : (($lifting->reward[0]->is_verified ?? "") == 2 ? 'Rejected' : 'Unverified'),
    //                             $starSaathiStatus,
    //                             $lifting->req_type == 2 ? $lifting->action_taken_at : '',
    //                             (count($userNames) > 0) ? implode(", ", $userNames) : ($lifting->reward[0]->user->name ?? ""),
    //                             $verified_by_at ? implode(", ", json_decode($verified_by_at)) : ($lifting->reward[0]->verified_by_at ?? ""),
    //                         ];
    //                         fputcsv($myfile,$content);
    //                     }
    //                     $fetchDataFrom += $fetchDataLimit;
    //                     $i += $fetchDataLimit;
    //                 }

    //                 // Release the lock
    //                 flock($myfile, LOCK_UN);
    //                 fclose($myfile);

    //                 // Replace old csv fileName with new tempFileName atomically
    //                 rename($tempFilePath, $finalFilePath);
    //             } else {
    //                 // Couldn't get the lock — another export is in progress
    //                 fclose($myfile);
    //                 LaravelLog::info('Export already in progress. Skipping for request '.$month["fileName"].'.');
    //             }
    //         }
    //     }
    //     catch (Exception $e) {
    //         LaravelLog::info('Error Occured. --> '.$e->getMessage());
    //     }
    //     LaravelLog::info('Automate Lifting Report Cron Execution ended');
    // }

    function automateVerifyLiftingReport()
    {
        LaravelLog::info('Automate Lifting Report Cron Execution started');
        set_time_limit(0);
        try{

            // Path where to store CSVs
            $path = public_path("/excel_exports/automate_verify_liftings");
            $months = [];

            //Current Month
            array_push($months, [
                "fromDate" => Carbon::now()->startOfMonth()->toDateString(),
                "toDate" => Carbon::now()->endOfMonth()->toDateString(),
                "fileName" => Carbon::now()->endOfMonth()->format("M-Y").".csv",
                "tempFileName" => "Temp-".Carbon::now()->endOfMonth()->format("M-Y").".csv",
            ]);

            //Previous Month
            array_push($months, [
                "fromDate" => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                "toDate" => Carbon::now()->subMonth()->endOfMonth()->toDateString(),
                "fileName" => Carbon::now()->subMonth()->endOfMonth()->format("M-Y").".csv",
                "tempFileName" => "Temp-".Carbon::now()->subMonth()->endOfMonth()->format("M-Y").".csv",
            ]);

            //2 Month Ago
            array_push($months, [
                "fromDate" => Carbon::now()->subMonths(2)->startOfMonth()->toDateString(),
                "toDate" => Carbon::now()->subMonth(2)->endOfMonth()->toDateString(),
                "fileName" => Carbon::now()->subMonth(2)->endOfMonth()->format("M-Y").".csv",
                "tempFileName" => "Temp-".Carbon::now()->subMonth(2)->endOfMonth()->format("M-Y").".csv",
            ]);

            // Make sure the export folder exists
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            foreach($months as $month)
            {
                $fromDate = $month["fromDate"];
                $toDate = $month["toDate"];
                $query = Lifting::with('product')->with('mason_user')->with('user')->with('reward')
                        ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$fromDate, $toDate]);
                $numberOfRecords = $query->count();
                set_time_limit(0);
                // $filename = "Verify_Liftings_".$this->getUniqueId().".csv";
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
                

                $tempFilePath = $path . '/' . $month["tempFileName"];
                $finalFilePath = $path . '/' . $month["fileName"];

                // Get a Binary Lock.
                $myfile = fopen($tempFilePath, "wb");

                // Try to get a non-blocking exclusive lock
                if (flock($myfile, LOCK_EX | LOCK_NB)) 
                {
                    fputcsv($myfile,$headings);
                    $fetchDataLimit = 1000;
                    $fetchDataFrom = 0;
                    $i = 0;
                    $dataProcessedCount = 0;
                    while($i < $numberOfRecords)
                    {
                        $data = $query->skip($fetchDataFrom)->take($fetchDataLimit)->get();
                        foreach($data as $lifting)
                        {
                            $stockStatus = '';
                            $liftingQuantity =  $lifting->qty ;
                            $stockAvailable  =   $lifting->available_stock ;

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
                            $isLiftingAsmOrStarSaathiQuery = LiftingApprovalHistory::where(["lifting_id" => $lifting->id]);
                            $isLiftingAsmOrStarSaathi = $isLiftingAsmOrStarSaathiQuery->get();
                            $asm = null;
                            foreach($isLiftingAsmOrStarSaathi as $val)
                            {
                                if($val->action_status == 0 && $val->seek_approval == 4)
                                {
                                    $asm = $val;
                                }
                            }
                            // $asm = LiftingApprovalHistory::where(['lifting_id' => $lifting->id, "action_status" => 0, "seek_approval" => 4])->orderBy("id", "DESC")->first();
                            $lifting_by = "";
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
                                    $lastModifiedBy = $user->roles->role_name ?? "";
                                    $lastModifiedDateTime = $lastModifiedRecord->created_at ?? "";
                                }
                            }
                            $attachment = $lifting->reward[0]->attachment ?? "";
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
                                    array_push($userNames, User::find($verified_by_id)->name ?? "");
                                }
                            }
                            $verified_by_at = $lifting->reward[0]->verified_by_at_history ?? false ;
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
                                (count($userNames) > 0) ? implode(", ", $userNames) : ($lifting->reward[0]->user->name ?? ""),
                                $verified_by_at ? implode(", ", json_decode($verified_by_at)) : ($lifting->reward[0]->verified_by_at ?? ""),
                            ];
                            fputcsv($myfile,$content);
                        }
                        $fetchDataFrom += $fetchDataLimit;
                        $i += $fetchDataLimit;
                    }

                    // Release the lock
                    flock($myfile, LOCK_UN);
                    fclose($myfile);

                    // Remove The Previous File.
                    if (file_exists($finalFilePath)) {
                        unlink($finalFilePath);
                    }

                    // Replace old csv fileName with new tempFileName atomically
                    rename($tempFilePath, $finalFilePath);

                } else {

                    // Couldn't get the lock — another export is in progress
                    fclose($myfile);
                    LaravelLog::info('Export already in progress. Skipping for request '.$month["fileName"].'.');
                }
            }


             return "Done";
        }
        catch (Exception $e) {
            LaravelLog::info('Error Occured. --> '.$e->getMessage());
        }
        LaravelLog::info('Automate Lifting Report Cron Execution ended');
    }

    function getSpecificMonthVerifyLiftingReport()
    {
        LaravelLog::info('Specific Lifting Report Cron Execution started');
        set_time_limit(0);
        try{
            // Path where to store CSVs
            $path = public_path("/excel_exports/verify_liftings");
            $months = [];
            //Current Month
            array_push($months, [
                "fromDate" => "2025-07-01",
                "toDate" => "2025-07-31",
                "fileName" => "July_2025.csv",
                "tempFileName" => "Temp_July_2025.csv",
            ]);
            

            // Make sure the export folder exists
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            foreach($months as $month)
            {
                $fromDate = $month["fromDate"];
                $toDate = $month["toDate"];
                $query = Lifting::with('product')->with('mason_user')->with('user')->with('reward')
                        ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$fromDate, $toDate]);
                $numberOfRecords = $query->count();
                set_time_limit(0);
                // $filename = "Verify_Liftings_".$this->getUniqueId().".csv";
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
                    'Action Taken At',
                    'verified_by',
                    'verified_at',
                ];
                

                $tempFilePath = $path . '/' . $month["tempFileName"];
                $finalFilePath = $path . '/' . $month["fileName"];

                $myfile = fopen($tempFilePath, "w");
                // Try to get a non-blocking exclusive lock
                if (flock($myfile, LOCK_EX | LOCK_NB)) 
                {
                    fputcsv($myfile,$headings);
                    $fetchDataLimit = 1000;
                    $fetchDataFrom = 0;
                    $i = 0;
                    $dataProcessedCount = 0;
                    while($i < $numberOfRecords)
                    {
                        $data = $query->skip($fetchDataFrom)->take($fetchDataLimit)->get();
                        foreach($data as $lifting)
                        {
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
                            $isLiftingAsmOrStarSaathiQuery = LiftingApprovalHistory::where(["lifting_id" => $lifting->id]);
                            $isLiftingAsmOrStarSaathi = $isLiftingAsmOrStarSaathiQuery->get();
                            $asm = null;
                            foreach($isLiftingAsmOrStarSaathi as $val)
                            {
                                if($val->action_status == 0 && $val->seek_approval == 4)
                                {
                                    $asm = $val;
                                }
                            }
                            // $asm = LiftingApprovalHistory::where(['lifting_id' => $lifting->id, "action_status" => 0, "seek_approval" => 4])->orderBy("id", "DESC")->first();
                            $lifting_by = "";
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
                                    $lastModifiedBy = $user->roles->role_name ?? "";
                                    $lastModifiedDateTime = $lastModifiedRecord->created_at ?? "";
                                }
                            }
                            $attachment = $lifting->reward[0]->attachment ?? "";
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
                                    array_push($userNames, User::find($verified_by_id)->name ?? "");
                                }
                            }
                            $verified_by_at = $lifting->reward[0]->verified_by_at_history ?? false ;
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
                                $lifting->req_type == 2 ? $lifting->action_taken_at : '',
                                (count($userNames) > 0) ? implode(", ", $userNames) : ($lifting->reward[0]->user->name ?? ""),
                                $verified_by_at ? implode(", ", json_decode($verified_by_at)) : ($lifting->reward[0]->verified_by_at ?? ""),
                            ];
                            fputcsv($myfile,$content);
                        }
                        $fetchDataFrom += $fetchDataLimit;
                        $i += $fetchDataLimit;
                    }

                    // Release the lock
                    flock($myfile, LOCK_UN);
                    fclose($myfile);

                    // Replace old csv fileName with new tempFileName atomically
                    rename($tempFilePath, $finalFilePath);
                } else {
                    // Couldn't get the lock — another export is in progress
                    fclose($myfile);
                    LaravelLog::info('Export already in progress. Skipping for request '.$month["fileName"].'.');
                }
            }
        }
        catch (Exception $e) {
            LaravelLog::info('Error Occured. --> '.$e->getMessage());
        }
        LaravelLog::info('Specific Lifting Report Cron Execution ended');
    }
    function automateVerifyLiftingAnnualReport()
    {
        set_time_limit(0);
        try{
            $months = [[
                "fromDate" => "2024-04-01",
                "toDate" => "2024-11-31",
                "fileName" => "Yearly verify lifting report"
            ]];
            foreach($months as $month)
            {
                $fromDate = $month["fromDate"];
                $toDate = $month["toDate"];
                $query = Lifting::with('product')->with('mason_user')->with('user')->with('reward')
                        ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$fromDate, $toDate]);
                $numberOfRecords = $query->count();
                set_time_limit(0);
                // $filename = "Verify_Liftings_".$this->getUniqueId().".csv";
                $filename = $month["fileName"].".csv";
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
                    'Action Taken At',
                    'verified_by',
                    'verified_at',
                ];

                $myfile = fopen(public_path("/excel_exports/automate_verify_liftings_yearly/").$filename, "w");
                fputcsv($myfile,$headings);
                $fetchDataLimit = 1000;
                $fetchDataFrom = 0;
                $i = 0;
                $dataProcessedCount = 0;
                while($i < $numberOfRecords)
                {
                    $data = $query->skip($fetchDataFrom)->take($fetchDataLimit)->get();
                    foreach($data as $lifting)
                    {
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
                        $isLiftingAsmOrStarSaathiQuery = LiftingApprovalHistory::where(["lifting_id" => $lifting->id]);
                        $isLiftingAsmOrStarSaathi = $isLiftingAsmOrStarSaathiQuery->get();
                        $asm = null;
                        foreach($isLiftingAsmOrStarSaathi as $val)
                        {
                            if($val->action_status == 0 && $val->seek_approval == 4)
                            {
                                $asm = $val;
                            }
                        }
                        // $asm = LiftingApprovalHistory::where(['lifting_id' => $lifting->id, "action_status" => 0, "seek_approval" => 4])->orderBy("id", "DESC")->first();
                        $lifting_by = "";
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
                                $lastModifiedBy = $user->roles->role_name ?? "";
                                $lastModifiedDateTime = $lastModifiedRecord->created_at ?? "";
                            }
                        }
                        $attachment = $lifting->reward[0]->attachment ?? "";
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
                                array_push($userNames, User::find($verified_by_id)->name ?? "");
                            }
                        }
                        $verified_by_at = $lifting->reward[0]->verified_by_at_history ?? false ;
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
                            $lifting->req_type == 2 ? $lifting->action_taken_at : '',
                            (count($userNames) > 0) ? implode(", ", $userNames) : ($lifting->reward[0]->user->name ?? ""),
                            $verified_by_at ? implode(", ", json_decode($verified_by_at)) : ($lifting->reward[0]->verified_by_at ?? ""),
                        ];
                        fputcsv($myfile,$content);
                    }
                    $fetchDataFrom += $fetchDataLimit;
                    $i += $fetchDataLimit;
                }
                fclose($myfile);
            }
        }
        catch (Exception $e) {
            LaravelLog::info('Error Occured. --> '.$e->getMessage());
        }
        LaravelLog::info('Automate Lifting Report Cron Executed');
    }
    function autoLiftingApproval($lifting)
    {
        //keep log
        $logData = [
            'user_id' => 0,
            'request' => json_encode($lifting),
            'action' => 'Auto Approved Lifting',
            'model_name' => 'Lifting, Reward',
        ];
        $logTable = Log::create($logData);
        //keep log
        try {
            DB::beginTransaction();
            $dealerAvailableStock =  $this->availStock($lifting->product_id, $lifting->user_id, $lifting->lifting_date);
            $currentMonthLiftings =  $this->getCurrentMonthLifting($lifting->product_id, $lifting->user_id, $lifting->lifting_date) ;
            
            $lifting->available_stock = $dealerAvailableStock - $currentMonthLiftings;
            $lifting->req_status = 1;
            $lifting->action_taken_at = now()->format('y-m-d H:i:s');
            $lifting->action_taken_by = 0;
            $lifting->save();
            
            $isVerified = 0;
            
             // As Per Client Requirement It Is Commented Out.
            // $isVerified = 1;
            // if(($dealerAvailableStock - $currentMonthLiftings) < $lifting->qty)
            // {
            //     $isVerified = 0;
            // }

            $rewards = Reward::where('lifting_id', $lifting->id)->get();
            $masonId = $rewards[0]->user_id;
            foreach($rewards as $reward)
            {
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
            DB::commit();
            $tables = json_encode([
                'Lifting' => $lifting,
                'Reward' => $rewards,
            ]);
            $logTable->update([
                'response' => $tables
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $logTable->update([
                'response' => $e->getMessage()
            ]);
        }

    }
    
    public function autoApproval($lifting, $point, $bonusPoint)
    {
        $this->autoLiftingApproval($lifting);
        $lifting->update([
            'seek_approval' => 3,
            'seek_approval_from' => Carbon::now(),
        ]);
        $currentTime = Carbon::now();
        $liftingApprovalHistory = [
            'lifting_id' => $lifting->id,
            'qty' => $lifting->qty,
            'point' => $point,
            'bonus_point' => $bonusPoint,
            'seek_approval' => 3,
            'seek_approval_by' => 0,
            'seek_approval_from' => $currentTime,
            'approval_window' => 0,
            'action_status' => 2,
            'action_taken_by' => 0,
        ];
        LiftingApprovalHistory::create($liftingApprovalHistory);
        //To keep approved history record.
        $liftingApprovalHistory = [
            'lifting_id' => $lifting->id,
            'qty' => $lifting->qty,
            'point' => $point,
            'bonus_point' => $bonusPoint,
            'seek_approval' => 3,
            'seek_approval_by' => 0,
            'seek_approval_from' => $currentTime,
            'approval_window' => 0,
            'action_status' => 3,
            'action_taken_by' => 0,
        ];
        LiftingApprovalHistory::create($liftingApprovalHistory);
        //Send Notification to Mason and Dealer
        if($lifting->req_by == null)
        {
            $msg = "Auto Approved Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
        }
        else
        {
            $msg = "Auto Approved Lifting of ".$lifting->qty." ".($lifting->product->name ?? null)." bags lifted by BD ".($lifting->reward[0]->mason->te_linked->name ?? null)." behalf of Mason ".($lifting->reward[0]->mason->name ?? null)." Phone No. ".($lifting->reward[0]->mason->phone ?? null);
        }
        $notificationData = [
            "notification_type" => "Lifting",
            "data" => [
                "msg" => $msg,
            ]
        ];
        Notification::send($lifting->reward[0]->mason ?? null, new StarLinkNotification($notificationData));//Mason
        Notification::send($lifting->user ?? null, new StarLinkNotification($notificationData));//Dealer
        //Send SMS to Mason
        $masonSMS = "Lifting Bags: ".$lifting->qty." ".($lifting->product->name ?? null)." Bags (".$lifting->reward[0]->mason->phone ?? null.") successfully Approved/Rejected: Approved. - Star Link";
        //Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$lifting->reward[0]->mason->phone.'&from=STARCM&text='.$masonSMS.'&dlr-mask=19&dlr-url');
    }
    public function automateLifting()
    {
        set_time_limit(0);
        try{
            $liftings = Lifting::where([
                // 'req_type' => 2,
                'req_status' => 0,
            ])->with('reward')->get();
            foreach($liftings as $lifting)
            {
                $point = 0;
                $bonusPoint = 0;
                foreach($lifting->reward as $reward)
                {
                    if($reward->is_bonus == 0){ 
                        $point = $reward->point; 
                    } 
                    else{ 
                        $bonusPoint = $reward->point;
                    }
                }
                if($lifting->req_by == null && $lifting->seek_approval == 1 && Carbon::parse($lifting->seek_approval_from)->diffInDays(now()) >= $this->settingVal('setting_name', 'dealer/rssd_approval_window'))
                {
                    $lifting->update([
                        'seek_approval' => 2,
                        'seek_approval_from' => Carbon::now(),
                    ]);
                    $teName = Reward::where(['lifting_id' => $lifting->id, 'is_bonus' => 0])->with('mason')->first();
                    $liftingApprovalHistory = [
                        'lifting_id' => $lifting->id,
                        'qty' => $lifting->qty,
                        'point' => $point,
                        'bonus_point' => $bonusPoint,
                        'seek_approval' => 2,
                        'seek_approval_by' => $teName->mason->parent ?? 0,
                        'seek_approval_from' => Carbon::now(),
                        'approval_window' => $this->settingVal('setting_name', 'bdo_approval_window'),
                        'action_status' => 2,
                        'action_taken_by' => 0,
                    ];
                    LiftingApprovalHistory::create($liftingApprovalHistory);
                }
                if($lifting->req_by != null && $lifting->seek_approval == 1 && Carbon::parse($lifting->seek_approval_from)->diffInDays(now()) >= $this->settingVal('setting_name', 'dealer/rssd_approval_window'))
                {
                    $this->autoApproval($lifting, $point, $bonusPoint);
                }
                if($lifting->seek_approval == 2 && Carbon::parse($lifting->seek_approval_from)->diffInDays(now()) >= $this->settingVal('setting_name', 'bdo_approval_window'))
                {
                    $this->autoApproval($lifting, $point, $bonusPoint);
                }
                if($lifting->seek_approval == 4 && Carbon::parse($lifting->seek_approval_from)->diffInDays(now()) >= $this->settingVal('setting_name', 'asm_approval_window'))
                {
                    $this->autoApproval($lifting, $point, $bonusPoint);
                }
            }
            // $this->info('Hourly Update has been send successfully');
        }
        catch (Exception $e) {
            LaravelLog::info('Error Occured. --> '.$e->getMessage());
        }
        LaravelLog::info('Cron Executed');
        return "Cron Sucessfully Executed.";
        // $this->info('Hourly Update has been send successfully');
    }
}
