<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lifting;
use App\Models\LiftingApprovalHistory;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\Setting;
use App\Models\Log;
use Exception;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutomateLifting extends Command
{
    use HelperTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lifting:automate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automate Liftings.';

    /**
     * Execute the console command.
     *
     * @return int
     */
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
            $isVerified = 1;
            if(($dealerAvailableStock - $currentMonthLiftings) < $lifting->qty)
            {
                $isVerified = 0;
            }
            $rewards = Reward::where('lifting_id', $lifting->id)->get();
            $masonId = $rewards[0]->id;
            foreach($rewards as $reward)
            {
                $isEligibleForLedgerInRewardTable = RewardHistory::ELIGIBLE_FOR_LEDGER_NO;
                $reward->is_verified = $isVerified;
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
    // public function settingVal($column, $val)
    // {
    //     return Setting::where($column, $val)->first()->setting_value;
    // }
    public function handle()
    {
        $liftings = Lifting::where([
            'req_type' => 2,
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
            if($lifting->seek_approval == 1 && Carbon::parse($lifting->seek_approval_from)->diffInDays(now()) >= $this->settingVal('setting_name', 'dealer/rssd_approval_window'))
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
                ];
                LiftingApprovalHistory::create($liftingApprovalHistory);
            }
            if($lifting->seek_approval == 2 && Carbon::parse($lifting->seek_approval_from)->diffInDays(now()) >= $this->settingVal('setting_name', 'bdo_approval_window'))
            {
                $this->autoLiftingApproval($lifting);
                $lifting->update([
                    'seek_approval' => 3,
                    'seek_approval_from' => Carbon::now(),
                ]);
                $liftingApprovalHistory = [
                    'lifting_id' => $lifting->id,
                    'qty' => $lifting->qty,
                    'point' => $point,
                    'bonus_point' => $bonusPoint,
                    'seek_approval' => 3,
                    'seek_approval_by' => 0,
                    'seek_approval_from' => Carbon::now(),
                    'approval_window' => 0,
                ];
                LiftingApprovalHistory::create($liftingApprovalHistory);
            }
        }
        // $this->info('Hourly Update has been send successfully');
    }
}
