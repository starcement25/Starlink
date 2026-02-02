<?php

namespace App\DataTables;

use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCatalogueRedeemtion;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class RedeemtionDashboardDataTable extends DataTable
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
                    ->editColumn('user.te_linked.name', function(UserCatalogueRedeemtion $data){
                        return $data->user->te_linked->name ?? "" ;
                    })
                    ->editColumn('user.branch.name', function (UserCatalogueRedeemtion $data) {
                        return $data->user->branch->name ?? "";
            
                    })
                    ->editColumn('user.name', function (UserCatalogueRedeemtion $data) {
                        return $data->user->name ?? "";
            
                    })
                    ->editColumn('user.phone', function (UserCatalogueRedeemtion $data) {
                        return $data->user->phone ?? "";
            
                    })
                    ->editColumn('user.te_linked.emp_code', function (UserCatalogueRedeemtion $data) {
                        return $data->user->te_linked->emp_code ?? "";
            
                    })
                    // ->addColumn('address', function (UserCatalogueRedeemtion $data) {
                    //     return $data->address1.",".$data->address2.",".$data->city.",".$data->district.",".$data->state.",".$data->country.",".$data->pincode;
            
                    // })

                    ->editColumn('address1', function(UserCatalogueRedeemtion $data){
                        return $data->address1 != "null" ? $data->address1: "" ;
                    })

                    ->editColumn('address2', function(UserCatalogueRedeemtion $data){
                        return $data->address2 != "null"  ? $data->address2: "" ;
                    })

                    ->editColumn('city', function(UserCatalogueRedeemtion $data){
                        return $data->city  != "null" ? $data->city: "" ;
                    })

                    ->editColumn('district', function(UserCatalogueRedeemtion $data){
                        return $data->district != "null"  ? $data->district: "" ;
                    })

                    ->editColumn('state', function(UserCatalogueRedeemtion $data){
                        return $data->state != "null" ?  $data->state : "" ;
                    })

                    ->editColumn('country', function(UserCatalogueRedeemtion $data){
                        return $data->country != "null" ?  $data->country: "" ;
                    })

                    ->editColumn('pincode', function(UserCatalogueRedeemtion $data){
                        return $data->pincode != "null" ? $data->pincode: "" ;
                    })

                    ->editColumn('delivery_date', function(UserCatalogueRedeemtion $data){
                        return isset($data->delivery_date) ? date('d-m-Y', strtotime($data->delivery_date)) : "" ;
                    })
                    ->editColumn('status', function(UserCatalogueRedeemtion $data){
                        $status = ['0'=> 'Pending','1' => 'Delivered', '2'=> 'Rejected'] ;
                        return $status[$data->status] ?? "" ;
                    })
                    ->editColumn('created_at', function(UserCatalogueRedeemtion $data){ 
                        $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at); 
                        return $formatedDate; 
                    })
                    ->rawColumns(['user.name','user.phone','user.te_linked.emp_code','user.address']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\UserCatalogueRedeemtion $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(UserCatalogueRedeemtion $model): QueryBuilder
    {
        return $model->newQuery()->whereNotNull('catalogue_id')->with('catalogue')
        ->with(['user','user.te_linked', 'user.branch'])
        ->whereIn('status', $this->status)
        ->whereHas('user',function($q){
            $q->where('parent', $this->parent_id)->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date));
        })
        ->whereHas('user.branch',function($q){
            $q->whereIn('id', $this->filteredBranch);
        })
        ->select([
            'user_catalogue_redeemtions.*'
        ])
        ->orderBy('id', "DESC");
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('redeemtion-table')
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
            Column::make('created_at')->title('Date'),
            Column::make('order_id')->title('Order No'),
            Column::make('user.name')->title('Mason Name'),
            Column::make('user.phone')->title('Mason Phone'),
            Column::make('user.te_linked.name')->title('Employee Name'),
            Column::make('user.te_linked.emp_code')->title('Employee Id'),
            Column::make('user.branch.name')->title('Branch')->searchable(false),
            // Column::make('address')->title('Delivery Address'),
            Column::make('address1'),
            Column::make('address2'),
            Column::make('city'),
            Column::make('district'),
            Column::make('state'),
            Column::make('country'),
            Column::make('pincode'),
            Column::make('catalogue.name')->title('Catalogue'),
            Column::make('status'),
            Column::make('delivery_date')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Redeemtion_' . date('YmdHis');
    }
}
