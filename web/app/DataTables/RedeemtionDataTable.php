<?php

namespace App\DataTables;

use App\Models\UserCatalogueRedeemtion;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RedeemtionDataTable extends DataTable
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
                    // ->editColumn('user.by_created.name', function(UserCatalogueRedeemtion $data){
                    //     return $data->user->by_created->name ?? "" ;
                    // })
                    ->editColumn('user.branch.name', function (UserCatalogueRedeemtion $data) {
                        return $data->user->branch->name ?? "";
            
                    })
                    ->editColumn('user.branch.zone.name', function (UserCatalogueRedeemtion $data) {
                        return $data->user->branch->zone->name ?? "";
            
                    })
                    ->editColumn('user.te_linked.emp_code', function (UserCatalogueRedeemtion $data) {
                        return $data->user->te_linked->emp_code ?? "";
            
                    })
                    ->editColumn('user.te_linked.name', function (UserCatalogueRedeemtion $data) {
                        return $data->user->te_linked->name ?? "";
            
                    })
                    ->editColumn('user.name', function (UserCatalogueRedeemtion $data) {
                        return $data->user->name ?? "";
            
                    })
                    ->editColumn('user.phone', function (UserCatalogueRedeemtion $data) {
                        return $data->user->phone ?? "";
            
                    })
                    ->editColumn('is_delivery_confirmed', function(UserCatalogueRedeemtion $data){
                        return $data->getDeliveryConfirmationStatus();
                    })
                    ->editColumn('delivery_confirmation_datetime', function(UserCatalogueRedeemtion $data){
                        return $data->delivery_confirmation_datetime;
                    })
                    // ->editColumn('user.by_created.emp_code', function (UserCatalogueRedeemtion $data) {
                    //     return $data->user->by_created->emp_code ?? "";
            
                    // })
                    // ->addColumn('address', function (UserCatalogueRedeemtion $data) {
                    //     return $data->address1.",".$data->address2.",".$data->city.",".$data->district.",".$data->state.",".$data->country.",".$data->pincode;
            
                    // })

                    ->editColumn('email', function(UserCatalogueRedeemtion $data){
                        // return (!empty($data->email ?? null) && $data->email != "null" && $data->catalogue->catalogue_type_id == 2) ? $data->email: "N/A" ;

                        return $data->email;
                    })

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

                    ->editColumn('catalogue.catalogueType.name', function(UserCatalogueRedeemtion $data){
                        return $data->catalogue->catalogueType->name ?? "" ;
                    })

                    ->editColumn('pincode', function(UserCatalogueRedeemtion $data){
                        return $data->pincode != "null" ? $data->pincode: "" ;
                    })

                    ->editColumn('delivery_date', function(UserCatalogueRedeemtion $data){
                        return isset($data->delivery_date) ? date('d-m-Y', strtotime($data->delivery_date)) : "" ;
                    })
                    ->editColumn('status', function(UserCatalogueRedeemtion $data){
                        // $status = ['0'=> 'Pending','1' => 'Delivered', '2'=> 'Rejected', '3' => 'Order Placed'] ;
                        // return $status[$data->status] ?? "" ;

                        return $data->getStatus();
                    })
                    ->editColumn('created_at', function(UserCatalogueRedeemtion $data){ 
                        $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at); 
                        return $formatedDate; 
                    })
                    ->editColumn('action', function(UserCatalogueRedeemtion $data){
                        return ' <div class="btn-group">
                        <a href="'.route('redeemtions.edit', ['redeemtion'=> $data->id]).'" class="btn btn-default btn-xs">
                            <i class="far fa-edit"></i>
                        </a>
                    </div>' ;
                    return $data->id;
                    })
                    ->rawColumns(['action','user.name','user.phone','user.by_created.emp_code','user.address']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\UserCatalogueRedeemtion $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(UserCatalogueRedeemtion $model): QueryBuilder
    {
        $loggedUser=Auth::user();
        if($loggedUser->role > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            return $model->newQuery()->whereNotNull('catalogue_id')->with('catalogue')->whereHas('user.branch', function ($query) use($allocated_branches){
                $query->whereIn('id',$allocated_branches);
            })
            ->with(['user', 'user.branch.zone', 'catalogue.catalogueType', 'user.by_created', 'user.te_linked', 'user.branch'])
            ->select([
                'user_catalogue_redeemtions.*'
            ])
            ->orderBy('id', "DESC");
        }
        else
        {
            return $model->newQuery()->whereNotNull('catalogue_id')->with('catalogue')
            ->with(['user', 'user.branch.zone', 'catalogue.catalogueType', 'user.by_created', 'user.te_linked', 'user.branch'])
            ->select([
                'user_catalogue_redeemtions.*'
            ])
            ->orderBy('id', "DESC");
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
                    ->setTableId('redeemtion-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
                        // 'dom'       => 'Bfrtip',
                        'dom'       => 'frtip',
                        'stateSave' => true,
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
            Column::make('id')->visible(false)->searchable(false)->exportable(false),
            Column::make('created_at')->title('Date'),
            Column::make('order_id')->title('Order No'),
            Column::make('user.name')->title('Contractor Name'),
            Column::make('user.phone')->title('Contractor Phone'),
            // Column::make('user.by_created.name')->title('Employee Name'),
            // Column::make('user.by_created.emp_code')->title('Employee Id'),
            Column::make('user.te_linked.emp_code')->title('BDE Code'),
            Column::make('user.te_linked.name')->title('BDE Name'),
            Column::make('user.branch.name')->title('Branch'),
            Column::make('user.branch.zone.name')->title('Zone'),
            // Column::make('address')->title('Delivery Address'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('address1'),
            Column::make('address2'),
            Column::make('city'),
            Column::make('district'),
            Column::make('state'),
            Column::make('country'),
            Column::make('pincode'),
            Column::make('catalogue.name')->title('Catalogue'),
            Column::make('catalogue.catalogueType.name')->title('Catalogue Type'),
            Column::make('status'),
            Column::make('is_delivery_confirmed')->title("Delivery Confirmed"),
            Column::make('delivery_confirmation_datetime')->title("Delivery Confirmed on"),
            Column::make('remarks'),
            Column::make('delivery_date'),
            Column::make('system_delivery_date_updated_at'),
            Column::make('catalogue_point'),
            Column::make('catalogue_tds_percentage')->title('TDS Percentage'),
            Column::make('catalogue_tds_point')->title('TDS Point'),
            Column::make('redeemed_point'),
            Column::make('action')->searchable(false),
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
