<?php

namespace App\DataTables;

use App\Models\Lifting;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class LiftingDashboardDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dataTable = new EloquentDataTable($query);
       
        $dataTable = $dataTable
        ->addColumn('status', function ($lifting) {
            $status = $lifting->reward[0]->is_verified == 1 ? '<span class="badge badge-success"> Verified</span>' : '<span class="badge badge-danger"> Unverified</span>' ;
         
            return $status  ;
          //  return 1  ;
         })
         ->addColumn('verified_by', function ($lifting) {
             return $lifting->reward[0]->user->name ?? "" ;
          
              
           //  return 1  ;
          })
         ->editColumn('product.name', function ($lifting) {
             return $lifting->product->name ?? ""  ;
          })
         ->editColumn('reward.point', function ($lifting) {
             // Lifting may has many point & return type array.
              $rewards = $lifting->reward ?? [] ;
              $totalPoint = 0 ;
             foreach ($rewards as $key => $row) {
                 $totalPoint += $row['point'] ;
             }

             return $totalPoint ;
          })
         ->addColumn('reward.attachment',function($lifting){
             return $lifting->reward[0]->attachment == null ? "No Attachment" : "<a href='".asset($lifting->reward[0]->attachment)."' target='_blank'> Open Attachment </a>";
         })
         ->editColumn('user.name',function($lifting){
             return $lifting->user->name ?? "";
         })
         ->addColumn('mason.name',function($lifting){
             return $lifting->mason_user->user->name ?? "";
         })
         ->addColumn('mason.phone',function($lifting){
             return $lifting->mason_user->user->phone ?? "";
         })
         ->addColumn('mason.branch',function($lifting){
             return $lifting->mason_user->user->branch->name ?? "";
         })
         ->editColumn('user.emp_code',function($lifting){
             return $lifting->user->emp_code ?? "";
         })
         ->addColumn('te.emp_code',function($lifting){
             return $lifting->mason_user->user->te_linked->emp_code ?? "";
         })
         ->addColumn('te.name',function($lifting){
             return $lifting->mason_user->user->te_linked->name ?? "";
         })
         ->addColumn('te.phone',function($lifting){
             return $lifting->mason_user->user->te_linked->phone ?? "";
         })
         ->addColumn('zone',function($lifting){
             return $lifting->mason_user->user->branch->zone->name ?? "";
         })
         ->rawColumns(["status","reward.attachment"]);
    
        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Lifting $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lifting $model): QueryBuilder
    {
        return $model->newQuery()
        ->with(['product' => function($q){
            $q->select('id', 'name');
        }])
        ->with(['user'=> function($query){
            $query->select('id', 'name','emp_code');
        }])
        ->whereHas('mason_user.user', function($q){
            $q->where('parent', $this->parent_id)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date));
        })
        ->whereHas('mason_user.user.branch', function($q){
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
        })
        ->whereHas('mason_user.user.branch.zone', function($q){
            $q->whereIn('id', $this->filteredZone);
        })
        ->whereHas('reward', function($q){
            $q->whereIn('is_verified', $this->lifting);
        })
        ->with(['mason_user'])
        ->whereIn('product_id', $this->product)
        ->orderBy('id', 'DESC');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('lifting-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
                        'dom'       => 'Bfrtip',
                        'stateSave' => true,
                        'order'     => [[0, 'desc']],
                        'buttons'   => ['excel'],
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
           
            Column::make('id')->title('id')->visible(false)->searchable(false)->exportable(false),
            Column::make('lifting_date')->title('Date'),
            Column::make('user.name')->title('Dealer'),
            Column::make('user.emp_code')->title('Dealer Code'),
            Column::make('mason.name')->title('Mason'),
            Column::make('mason.phone')->title('Mason Mobile'),
            Column::make('mason.branch')->title('Mason Branch')->searchable(false),
            Column::make('te.emp_code')->title('TE Code'),
            Column::make('te.name')->title('TE Name'),
            Column::make('te.phone')->title('TE Phone'),
            Column::make('zone')->title('Zone')->searchable(false),
            Column::make('product.name'),
            Column::make('qty')->title('Quantity')->searchable(false),
            Column::make('reward.point')->title('Point')->searchable(false),
            Column::make('reward.attachment')->title('Attachment')->searchable(false),
            Column::make('status'),
            Column::make('verified_by')->title('verified_by'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Lifting_' . date('YmdHis');
    }
}
