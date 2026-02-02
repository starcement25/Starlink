<?php

namespace App\DataTables;

use App\Models\Support;
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

class SupportDashboardDataTable extends DataTable
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
            ->editColumn('order.user.te_linked.name', function($data){
                return $data->order->user->te_linked->name ?? "" ;
            })
            ->editColumn('order.user.branch.name', function ($data) {
                return $data->order->user->branch->name ?? "";
    
            })
            ->editColumn('updated_at', function($data){
                return isset($data->updated_at) ? date('d-m-Y h:i:s', strtotime($data->updated_at)) : "" ;
            })
            ->editColumn('support_type', function($data){
                $type = ['1' => 'Not Delivered', '2'=> 'Defective Product'] ;
                return $type[$data->support_type] ?? "" ;
            })
            ->editColumn('status', function ($data) {
                $class = $data->status == 1 ? 'badge-warning' : ($data->status == 2 ? 'badge-success' : 'badge-danger') ;
                $text  = $data->status == 1 ? 'Pending' : ($data->status == 2 ? 'Resolved' : 'Rejected') ;
            
                return '<span class="badge '.$class.'">'.$text.'</span>';
            })
            ->editColumn('order.order_id',function($data)
            {
                return $data->order->order_id ?? "";
            })
            ->editColumn('order.user.name',function($data)
            {
                return $data->order->user->name ?? "";
            })
            ->editColumn('order.user.phone',function($data)
            {
                return $data->order->user->phone ?? "";
            })
            ->editColumn('order.user.te_linked.emp_code',function($data)
            {
                return $data->order->user->te_linked->emp_code ?? "";
            })
            
            ->rawColumns(['status'])
           ;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Support $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Support $model): QueryBuilder
    {
        // return $model->newQuery()->whereNotNull('catalogue_id')->with(['user','user.te_linked', 'user.branch'])->select([
        //     'user_catalogue_redeemtions.*'
        // ])->orderBy('id', "DESC");
        return $model->newQuery()->with(['order', 'order.user', 'order.user.branch','order.user.te_linked'])
        ->whereHas('order.user', function($q){
            $q->where('parent', $this->parent_id)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date));
        })
        ->whereHas('order.user.branch', function($q){
            $q->whereIn('id', $this->filteredBranch);
        })
        ->whereIn('status', $this->status)
        ->select(['supports.*'])->orderBy('id', "DESC");
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('supportmaster-table')
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
          
            Column::make('id')->visible(false)->searchable(false)->exportable(false),
            Column::make('order.order_id')->title('Order No'),
            Column::make('order.user.name')->title('Mason Name'),
            Column::make('order.user.phone')->title('Mason Phone'),
            Column::make('order.user.te_linked.name')->title('Employee Name'),
            Column::make('order.user.te_linked.emp_code')->title('Employee Id'),
            Column::make('order.user.branch.name')->title('Branch')->searchable(false),
            Column::make('support_type')->title('Type'),
            Column::make('comment'),
            Column::make('status'),
            Column::make('updated_at')->title('Date'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'SupportMaster_' . date('YmdHis');
    }
}
