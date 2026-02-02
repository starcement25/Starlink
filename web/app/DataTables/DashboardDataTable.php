<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Reward;
use App\Models\Lifting;
use App\Models\Support;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\RejectedRedeemtion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCatalogueRedeemtion;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class DashboardDataTable extends DataTable
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
            ->addColumn('employee_branch.name', function (User $user) {
                $data = $user->employee_branch->pluck('name')->toArray() ?? "" ;
                if($data != "")
                {
                    return  implode(",",$data);
                }
                return $data;
            })
            ->editColumn('employee_branch.zone.name', function (User $user) {
                 $datas = $user->employee_branch->all() ;
                 $temp= [] ;
                 foreach($datas as $data){
                    $temp[] = $data['zone']['name'];
                 }
              return  implode(",",array_unique($temp));
            })
            ->editColumn('total_linked_mason', function (User $user) {
                 return '<a href="'.route('admin.mason.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date]).'">'.count(User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('active_mason', function (User $user) {
                 return '<a href="'.route('admin.mason.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'isActive' => [1]]).'">'.count(User::where(['role' => 2, 'parent' => $user->id, 'status' => 1])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('verified_lifting', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.lifting.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'lifting' => [1], 'product' => [1,2]]).'">'.count(Reward::whereIn('user_id',$masonIds)->where(['is_verified' => 1, 'is_bonus' => 0])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('unverified_lifting', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.lifting.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'lifting' => [0], 'product' => [1,2]]).'">'.count(Reward::whereIn('user_id',$masonIds)->where(['is_verified' => 0, 'is_bonus' => 0])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('total_ppc_lifting_bag', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.lifting.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'lifting' => [0,1], 'product' => [1]]).'">'.Reward::with('lifting')->whereIn('user_id',$masonIds)->where(['is_bonus' => 0])->whereHas('lifting',function($q){
                    $q->where('product_id',1);
                })->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->sum('bag').'</a>';
            })
            ->editColumn('total_arc_lifting_bag', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.lifting.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'lifting' => [0,1], 'product' => [2]]).'">'.Reward::with('lifting')->whereIn('user_id',$masonIds)->where(['is_bonus' => 0])->whereHas('lifting',function($q){
                    $q->where('product_id',2);
                })->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->sum('bag').'</a>';
            })
            ->editColumn('mason_net_point', function (User $user) {
                if($this->from_date === '1990-12-01' && $this->to_date === date('Y-m-d'))
                {
                    return '<a href="'.route('admin.mason.point.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date]).'">'.User::where(['role' => 2, 'parent' => $user->id])->sum('points').'</a>';
                }
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.mason.point.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date]).'">'.$this->masonNetPoint($masonIds).'</a>';
            })
            ->editColumn('gift_redeemed', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.redeemtion.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'status' => [0,1,2]]).'">'.count(UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('gift_pending', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.redeemtion.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'status' => [0]]).'">'.count(UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->where('status', 0)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('gift_delivered', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.redeemtion.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'status' => [1]]).'">'.count(UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->where('status', 1)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('query_raised', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.support.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'status' => [1,2,3]]).'">'.
                count(Support::with('order')->whereHas('order',function($q) use($masonIds){
                    $q->whereIn('user_id', $masonIds);
                })->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('query_pending', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.support.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'status' => [1]]).'">'.
                count(Support::with('order')->whereHas('order',function($q) use($masonIds){
                    $q->whereIn('user_id', $masonIds);
                })->where('status', 1)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->editColumn('query_resolved', function (User $user) {
                $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->pluck('id')->toArray();
                return '<a href="'.route('admin.support.dashboard',['filteredZone' => $this->filteredZone, 'filteredBranch' => $this->filteredBranch, 'parent_id' => $user->id, 'fromDate' => $this->from_date, 'toDate' => $this->to_date, 'status' => [2]]).'">'.
                count(Support::with('order')->whereHas('order',function($q) use($masonIds){
                    $q->whereIn('user_id', $masonIds);
                })->where('status', 2)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->get()).'</a>';
            })
            ->rawColumns([
                'total_linked_mason',
                'active_mason',
                'mason_net_point',
                'verified_lifting',
                'unverified_lifting',
                'total_ppc_lifting_bag',
                'total_arc_lifting_bag',
                'gift_redeemed',
                'gift_pending',
                'gift_delivered',
                'query_raised',
                'query_pending',
                'query_resolved'
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Employee $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->whereHas('employee_branch', function($q){
            $q->whereIn('branch_id', $this->filteredBranch)->select([
                "branch.id AS branch_id",
                "branch.zone_id",
                "branch.name",
                "branch.branch_code",
                "branch.state_id",
                "branch.description",
                "branch.status",
                "branch.created_at",
                "branch.updated_at",
            ]);
        })->whereHas('employee_branch.zone', function($q){
            $q->whereIn('id', $this->filteredZone);
        })->where('role', 1)->where('status', 1)->orderBy('id', 'DESC')->select(["users.*"]);
    }



    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('employee-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
                        'dom'        => 'Bfrtip',
                        'stateSave'  => true,
                        // 'serverSide' => false,
                        'order'      => [[0, 'desc']],
                        'buttons'    => ['excel'],
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
            Column::make('id')->visible(false)->searchable(false)->orderable(false)->exportable(false),
            Column::make('emp_code')->orderable(false),
            Column::make('name')->orderable(false),
            Column::make('employee_branch.name')->title('Branch')->orderable(false)->searchable(false),
            Column::make('employee_branch.zone.name')->title('Zone')->orderable(false)->searchable(false),
            Column::make('total_linked_mason')->searchable(false)->orderable(false),
            Column::make('active_mason')->searchable(false)->orderable(false),
            Column::make('verified_lifting')->searchable(false)->orderable(false),
            Column::make('unverified_lifting')->searchable(false)->orderable(false),
            Column::make('total_ppc_lifting_bag')->searchable(false)->orderable(false),
            Column::make('total_arc_lifting_bag')->searchable(false)->orderable(false),
            Column::make('mason_net_point')->searchable(false)->orderable(false)->exportFormat('0'),
            Column::make('gift_redeemed')->searchable(false)->orderable(false),
            Column::make('gift_pending')->searchable(false)->orderable(false),
            Column::make('gift_delivered')->searchable(false)->orderable(false),
            Column::make('query_raised')->searchable(false)->orderable(false),
            Column::make('query_pending')->searchable(false)->orderable(false),
            Column::make('query_resolved')->searchable(false)->orderable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Employee_' . date('YmdHis');
    }
    protected function masonNetPoint($masonIds): int
    {
        $rewardPoint        = Reward::selectRaw("IFNULL(SUM(point), 0) AS `point`")->whereIn('user_id', $masonIds)
                                ->where('is_verified', 1)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->value('point') ;
        
        $redeemedPoint      =   UserCatalogueRedeemtion::selectRaw("IFNULL(SUM(redeemed_point), 0) AS `redeemed_point`")
                                ->whereIn('user_id', $masonIds)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->value('redeemed_point') ;
        
        $rejectionCreditPoint = RejectedRedeemtion::selectRaw("IFNULL(SUM(point_credited), 0) AS `point_credited`")
                                ->whereIn('user_id', $masonIds)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))->value('point_credited') ;

        return $points = ($rewardPoint - $redeemedPoint + $rejectionCreditPoint) ;
    }
}
