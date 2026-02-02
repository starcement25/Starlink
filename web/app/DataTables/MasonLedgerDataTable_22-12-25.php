<?php

namespace App\DataTables;

use App\Models\UserCatalogueRedeemtion;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\RejectedRedeemtion;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MasonLedgerDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        
        ->editColumn('created_at', function ($data) {

            return $data->created_at->toDateString() ?? "";
        })
        ->editColumn('lifting_date', function ($data) {

            // return (Carbon::parse($data->lifting_date ?? null)->toDateString() ?? "");
            return !empty($data?->lifting_date) ? Carbon::parse($data->lifting_date)->toDateString() : "N/A";
        })
        ->editColumn('mason_details.name', function ($data) {

            $mason_details = json_decode($data->mason_details);
            return $mason_details == null ? "" : $mason_details->name;
        })
        ->editColumn('mason_details.phone', function ($data) {

            $mason_details = json_decode($data->mason_details);
            return $mason_details == null ? "" : $mason_details->phone;
        })
        ->editColumn('mason_details.branch.name', function ($data) {

            $mason_details = json_decode($data->mason_details);
            return $mason_details == null ? "" : ($mason_details->branch == null ? "" : $mason_details->branch->name);
        })
        ->editColumn('mason_details.te.code', function ($data) {

            $mason_details = json_decode($data->mason_details);
            return $mason_details == null ? "" : ($mason_details->te == null ? "" : $mason_details->te->code);
        })
        ->editColumn('mason_details.te.name', function ($data) {

            $mason_details = json_decode($data->mason_details);
            return $mason_details == null ? "" : ($mason_details->te == null ? "" : $mason_details->te->name);
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\UserCatalogueRedeemtion $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): QueryBuilder
    {
        $loggedUser=Auth::user();
        $user     = $this->user ; 
        if(($this->selectedDateFrom !== null) && ($this->selectedDateTo !== null))
        {
            if($user == "ALL"){
                if($loggedUser->role > 6)
                {
                    $allocated_branches=json_decode($loggedUser->allocated_branches);
                    $userIds=implode(",",User::whereIn('branch_id',$allocated_branches)->pluck('id')->toArray());
                    //for specific branch users those who belongs to logged user branch
                    $userCatalogueRedeemtions = UserCatalogueRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,created_at AS ledger_date,NULL AS `lifting_date`")->whereIn('user_id', $userIds)->whereBetween('created_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59']);
                    $rewards = Reward::selectRaw("get_mason_details(`rewards`.`user_id`) AS mason_details,'' AS `order_id`,`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
                            rewards.updated_at AS ledger_date, `lifting`.`lifting_date`")->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->whereIn('rewards.user_id', $userIds)->whereBetween('rewards.updated_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->where('rewards.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($userCatalogueRedeemtions);
                    $rewardHistory = RewardHistory::selectRaw("get_mason_details(`reward_history`.`user_id`) AS mason_details,'' AS `order_id`,`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
                            reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date`")->leftJoin('rewards', 'reward_history.reward_id', '=', 'rewards.id')->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->whereIn('reward_history.user_id', $userIds)->whereBetween('reward_history.reward_date_time', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->where('reward_history.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($rewards);
                    $rejectedRedeemtions = RejectedRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,'' AS `order_id`,`user_id`, `point_credited` AS `credit_point`,'' AS `debit_point`,`description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->whereIn('user_id', $userIds)->whereBetween('created_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->union($rewardHistory)->orderBy('ledger_date');
                    return $rejectedRedeemtions;
                }
                else
                {
                    //for all branch users
                    $userCatalogueRedeemtions = UserCatalogueRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->whereBetween('created_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59']);

                    $rewards = Reward::selectRaw("get_mason_details(`rewards`.`user_id`) AS mason_details,'' AS `order_id`,`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
                            rewards.updated_at AS ledger_date, `lifting`.`lifting_date`")->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->whereBetween('rewards.updated_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->where('rewards.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($userCatalogueRedeemtions);

                    $rewardHistory = RewardHistory::selectRaw("get_mason_details(`reward_history`.`user_id`) AS mason_details,'' AS `order_id`,`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
                            reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date`")->leftJoin('rewards', 'reward_history.reward_id', '=', 'rewards.id')->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->whereBetween('reward_history.reward_date_time', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->where('reward_history.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($rewards);

                    $rejectedRedeemtions = RejectedRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,'' AS `order_id`,`user_id`, `point_credited` AS `credit_point`,'' AS `debit_point`,`description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->whereBetween('created_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->union($rewardHistory)->orderBy('ledger_date');
                    return $rejectedRedeemtions;
                }

            }else{
                $userCatalogueRedeemtions = UserCatalogueRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->where('user_id', $user)->whereBetween('created_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59']);
                $rewards = Reward::selectRaw("get_mason_details(`rewards`.`user_id`) AS mason_details,'' AS `order_id`,`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
                            rewards.updated_at AS ledger_date, `lifting`.`lifting_date`")->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->whereBetween('rewards.updated_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->where('rewards.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->where('rewards.user_id', $user)->union($userCatalogueRedeemtions);
                $rewardHistory = RewardHistory::selectRaw("get_mason_details(`reward_history`.`user_id`) AS mason_details,'' AS `order_id`,`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
                            reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date`")->leftJoin('rewards', 'reward_history.reward_id', '=', 'rewards.id')->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->where('reward_history.user_id', $user)->whereBetween('reward_history.reward_date_time', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->where('reward_history.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($rewards);
                $rejectedRedeemtions = RejectedRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,'' AS `order_id`,`user_id`, `point_credited` AS `credit_point`,'' AS `debit_point`,`description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->where('user_id', $user)->whereBetween('created_at', [$this->selectedDateFrom . ' 00:00:00', $this->selectedDateTo . ' 23:59:59'])->union($rewardHistory)->orderBy('ledger_date');
                return $rejectedRedeemtions;
            }
        }
        else
        {
            if($user == "ALL"){
                if($loggedUser->role > 6)
                {
                    $allocated_branches=json_decode($loggedUser->allocated_branches);
                    $userIds=implode(",",User::whereIn('branch_id',$allocated_branches)->pluck('id')->toArray());
                    //for specific branch users those who belongs to logged user branch
                    $userCatalogueRedeemtions = UserCatalogueRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,created_at AS ledger_date,NULL AS `lifting_date`")->whereIn('user_id', $userIds);
                    $rewards = Reward::selectRaw("get_mason_details(`rewards`.`user_id`) AS mason_details,'' AS `order_id`,`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
                            rewards.updated_at AS ledger_date, `lifting`.`lifting_date`")->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->whereIn('rewards.user_id', $userIds)->where('rewards.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($userCatalogueRedeemtions);
                    $rewardHistory = RewardHistory::selectRaw("get_mason_details(`reward_history`.`user_id`) AS mason_details,'' AS `order_id`,`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
                            reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date`")->leftJoin('rewards', 'reward_history.reward_id', '=', 'rewards.id')->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->whereIn('reward_history.user_id', $userIds)->where('reward_history.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($rewards);
                    $rejectedRedeemtions = RejectedRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,'' AS `order_id`,`user_id`, `point_credited` AS `credit_point`,'' AS `debit_point`,`description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->whereIn('user_id', $userIds)->union($rewardHistory)->orderBy('ledger_date');
                    return $rejectedRedeemtions;
                }
                else
                {
                    //for all branch users
                    $userCatalogueRedeemtions = UserCatalogueRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`");

                    $rewards = Reward::selectRaw("get_mason_details(`rewards`.`user_id`) AS mason_details,'' AS `order_id`,`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
                            rewards.updated_at AS ledger_date, `lifting`.`lifting_date`")->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->where('rewards.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($userCatalogueRedeemtions);

                    $rewardHistory = RewardHistory::selectRaw("get_mason_details(`reward_history`.`user_id`) AS mason_details,'' AS `order_id`,`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
                            reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date`")->leftJoin('rewards', 'reward_history.reward_id', '=', 'rewards.id')->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->where('reward_history.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($rewards);

                    $rejectedRedeemtions = RejectedRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,'' AS `order_id`,`user_id`, `point_credited` AS `credit_point`,'' AS `debit_point`,`description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->union($rewardHistory)->orderBy('ledger_date');
                    return $rejectedRedeemtions;
                }

            }else{
              
                $userCatalogueRedeemtions = UserCatalogueRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,IFNULL(`order_id`,'') AS `order_id`, `user_id`, '' AS `credit_point`,`redeemed_point` AS `debit_point`,IFNULL(`description`, '') AS `description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->where('user_id', $user);
                $rewards = Reward::selectRaw("get_mason_details(`rewards`.`user_id`) AS mason_details,'' AS `order_id`,`rewards`.`user_id`,CASE WHEN `rewards`.`is_verified` = ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS credit_point,CASE WHEN `rewards`.`is_verified` != ".(Reward::VERIFIED)." THEN `rewards`.`point` ELSE '' END AS debit_point,`rewards`.`description`,`rewards`.`created_at`,
                            rewards.updated_at AS ledger_date, `lifting`.`lifting_date`")->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->where('rewards.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->where('rewards.user_id', $user)->union($userCatalogueRedeemtions);
                $rewardHistory = RewardHistory::selectRaw("get_mason_details(`reward_history`.`user_id`) AS mason_details,'' AS `order_id`,`reward_history`.`user_id`,CASE WHEN `reward_history`.`is_verified` = ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS credit_point,CASE WHEN `reward_history`.`is_verified` != ".(Reward::VERIFIED)." THEN `reward_history`.`point` ELSE '' END AS debit_point,`reward_history`.`description`,`reward_history`.`created_at`,
                            reward_history.reward_date_time AS ledger_date, `lifting`.`lifting_date`")->leftJoin('rewards', 'reward_history.reward_id', '=', 'rewards.id')->leftJoin('lifting', 'rewards.lifting_id', '=', 'lifting.id')->where('reward_history.user_id', $user)->where('reward_history.is_eligible_for_ledger', RewardHistory::ELIGIBLE_FOR_LEDGER_YES)->union($rewards);
                $rejectedRedeemtions = RejectedRedeemtion::selectRaw("get_mason_details(`user_id`) AS mason_details,'' AS `order_id`,`user_id`, `point_credited` AS `credit_point`,'' AS `debit_point`,`description`,`created_at`,
                            created_at AS ledger_date, NULL AS `lifting_date`")->where('user_id', $user)->union($rewardHistory)->orderBy('ledger_date');
                return $rejectedRedeemtions;
            }
        }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('customerlifting-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
                        'dom'       => 'frtip',
                        'stateSave' => false,
                        'searching' => false,
                        'order'     => [[0, 'desc']],
                        'buttons'   => ['excel', 'csv'],
                            // Enable Buttons as per your need
            //                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
            //                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
            //                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner',],
            //                    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
            //                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],

                    ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('lifting_date')->title('Lifting Date')->orderable(false),
            Column::make('created_at')->title('Creation Date')->orderable(false),
            Column::make('order_id')->title('Order No')->orderable(false),
            Column::make('mason_details.name')->title('Name')->orderable(false),
            Column::make('mason_details.phone')->title('Phone No.')->orderable(false),
            Column::make('mason_details.branch.name')->title('Branch')->orderable(false),
            Column::make('mason_details.te.code')->title('BDE Code')->orderable(false),
            Column::make('mason_details.te.name')->title('BDE Name')->orderable(false),
            Column::make('description')->orderable(false),
            Column::make('credit_point')->title('Credit Point')->orderable(false),
            Column::make('debit_point')->title('Debit Point')->orderable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'MasonLedger_' . date('YmdHis');
    }
}
