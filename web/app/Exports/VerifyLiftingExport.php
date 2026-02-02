<?php

namespace App\Exports;

use App\Models\Lifting;
use App\Models\LiftingApprovalHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class VerifyLiftingExport implements FromQuery,WithMapping,WithHeadings
{
    use Exportable;
    
    public function forFromDate(string $fromDate)
    {
        $this->fromDate = $fromDate;
        return $this;
    }
    
    public function forToDate(string $toDate)
    {
        $this->toDate = $toDate;
        return $this;
    }
    public function forUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function headings(): array
    {
        return[
            'Date',
            'Dealer',
            'Dealer Code',
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
            'Lifting Creaion Date and Time',
            'Point',
            'Attachment',
            'Status',
            'Star Saathi Status',
            'Action Taken At',
            'verified_by',
            'verified_at',
        ];
    }

    public function map($lifting): array
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
        if($lifting->req_type == 2)
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
            $masonSubmitedQty = LiftingApprovalHistory::where([
                'lifting_id' => $lifting->id,
                'action_status' => 0
            ])->first()->qty ?? "";
            $dealerEditedQtys = LiftingApprovalHistory::where([
                'lifting_id' => $lifting->id,
                'action_status' => 1,
            ])->get();
            foreach($dealerEditedQtys as $val)
            {
                $user = User::find($val->action_taken_by);
                if(in_array($user->role, [3,4,6]))
                {
                    $dealerEditedQty = $val->qty;
                }
            }
            $bdEditedQtys = LiftingApprovalHistory::where([
                'lifting_id' => $lifting->id,
                'action_status' => 1,
            ])->get();
            foreach($bdEditedQtys as $val)
            {
                $user = User::find($val->action_taken_by);
                if($user->role == 1)
                {
                    $bdEditedQty = $val->qty;
                }
            }
            $lastModifiedId = LiftingApprovalHistory::where([
                'lifting_id' => $lifting->id,
                'action_status' => 3
            ])->orderBy('id', 'DESC')->first()->action_taken_by ?? "";
            $lastModifiedBy = User::find($lastModifiedId)->roles->role_name ?? "";
            $lastModifiedDateTime = LiftingApprovalHistory::where([
                'lifting_id' => $lifting->id,
                'action_status' => 3
            ])->orderBy('id', 'DESC')->first()->created_at ?? "";
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
        if($lifting->req_type == 2 && $lifting->req_status == 1 && $lifting->seek_approval == 3)
        {
            $autoLiftingApproval = "Yes";
        }
         return[
             $lifting->lifting_date ?? "",
             $lifting->user->name ?? "",
             $lifting->user->emp_code ?? "",
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
             $lifting->created_at ?? "",
             $totalPoint ?? "",
            //  $lifting->reward[0]->attachment ?? "",
            $attachment,
             $lifting->reward[0]->is_verified ?? "" == 1 ? 'Verified' : 'Unverified',
             $starSaathiStatus,
             $lifting->req_type == 2 ? $lifting->action_taken_at : '',
             $lifting->reward[0]->user->name ?? "",
             $lifting->reward[0]->verified_by_at ?? "",
         ];
    }

    public function query()
    {
        if($this->userId == "ALL")
        {
            return  Lifting::with('product')->with('mason_user')->with('user')->with('reward')
                    ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$this->fromDate, $this->toDate]);
        }
        else
        {
            return  Lifting::with('product')->with('mason_user')->with('user')->with('reward')->whereIn('id', function($q){
                $q->select('lifting_id')->from('rewards')->where('user_id', $this->userId);
           })->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$this->fromDate, $this->toDate]);
        }
        
    }
}
