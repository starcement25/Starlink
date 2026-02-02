<?php

namespace App\DataTables;

use App\Models\CustomerLifting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class CustomerLiftingDataTable extends DataTable
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
        ->editColumn('status', function ($data) {
            $class = $data->status == 1 ? 'badge-success' : 'badge-danger' ;
            $text  = $data->status == 1 ? 'Active' : 'Disabled' ;
           
            return '<span class="badge '.$class.'">'.$text.'</span>';
        })
        ->editColumn('dealer.dealer_linked.name', function ($data) {
           
            return $data->dealer->dealer_linked->name ?? "";
        })
        ->editColumn('dealer.name', function ($data) {
           
            return $data->dealer->name ?? "";
        })
        ->editColumn('dealer.emp_code', function ($data) {
           
            return $data->dealer->emp_code ?? "";
        })
        ->editColumn('dealer.sap_code', function ($data) {
           
            return $data->dealer->sap_code ?? "";
        })
        ->editColumn('product.name', function ($data) {
           
            return $data->product->name ?? "";
        })
        ->editColumn('dealer.branch.name', function ($data) {
           
            return $data->dealer->branch->name ?? "";
        })
        ->editColumn('branch.branch_code', function ($data) {
           
            return $data->branch->branch_code ?? "" ;
        })
        ->editColumn('status', function ($data) {
            $class = $data->status == 1 ? 'badge-success' : 'badge-danger' ;
            $text  = $data->status == 1 ? 'Active' : 'Disabled' ;
           
            return '<span class="badge '.$class.'">'.$text.'</span>';
        })
        ->editColumn('action', function ($data) {
            return '<form method="POST" action="'.route('customer-stock.destroy', ['customer_stock'=> $data->id]).'">
                       '.csrf_field().'
                        <input name="_method" type="hidden" value="DELETE">
                        <div class="btn-group">
                            <a href="'.route('customer-stock.edit', ['customer_stock'=> $data->id]).'" class="btn btn-default btn-xs">
                                <i class="far fa-edit"></i>
                            </a>
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </form>';
         })
        ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\CustomerLifting $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(CustomerLifting $model): QueryBuilder
    {
        $loggedUser=Auth::user();
        if($loggedUser->role > 6)
        {
        $allocated_branches=json_decode($loggedUser->allocated_branches);
        $dealerIds=User::whereIn('branch_id',$allocated_branches)->pluck('id');
        return $model->newQuery()->with(['dealer','dealer.branch', 'dealer.dealer_linked'])->with('product')->orderBy('id', 'DESC')->select([
        'customer_liftings.*',
        \DB::raw("(
            CASE WHEN customer_liftings.month = 1 THEN 'January' 
            WHEN customer_liftings.month = 2 THEN 'February'
            WHEN customer_liftings.month = 3 THEN 'March'
            WHEN customer_liftings.month = 4 THEN 'April'
            WHEN customer_liftings.month = 5 THEN 'May'
            WHEN customer_liftings.month = 6 THEN 'June'
            WHEN customer_liftings.month = 7 THEN 'July'
            WHEN customer_liftings.month = 8 THEN 'August'
            WHEN customer_liftings.month = 9 THEN 'September'
            WHEN customer_liftings.month = 10 THEN 'October'
            WHEN customer_liftings.month = 11 THEN 'November'
            WHEN customer_liftings.month = 12 THEN 'December'
            ELSE 0 END) AS `month_name`")
            
        ])->whereIn('dealer_id',$dealerIds);
        }
        else
        {
            return $model->newQuery()->with(['dealer','dealer.branch', 'dealer.dealer_linked'])->with('product')->orderBy('id', 'DESC')->select([
                'customer_liftings.*',
                \DB::raw("(
                    CASE WHEN customer_liftings.month = 1 THEN 'January' 
                    WHEN customer_liftings.month = 2 THEN 'February'
                    WHEN customer_liftings.month = 3 THEN 'March'
                    WHEN customer_liftings.month = 4 THEN 'April'
                    WHEN customer_liftings.month = 5 THEN 'May'
                    WHEN customer_liftings.month = 6 THEN 'June'
                    WHEN customer_liftings.month = 7 THEN 'July'
                    WHEN customer_liftings.month = 8 THEN 'August'
                    WHEN customer_liftings.month = 9 THEN 'September'
                    WHEN customer_liftings.month = 10 THEN 'October'
                    WHEN customer_liftings.month = 11 THEN 'November'
                    WHEN customer_liftings.month = 12 THEN 'December'
                    ELSE 0 END) AS `month_name`")
                    
                ]);
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
           
            Column::make('id')->visible(false),
            Column::make('lifting_code')->title('Code'),
            Column::make('dealer.name'),
            Column::make('dealer.emp_code')->title('Dealer Code'),
            Column::make('dealer.sap_code')->title('SAP Code'),
            Column::make('year'),
            Column::make('dealer.branch.name')->title('Branch'),
            Column::make('month_name')->searchable(false)->title('Month'),
            Column::make('dealer.dealer_linked.name')->title('Linked Dealer'),
            Column::make('product.name'),
            Column::make('quantity'),
            Column::make('status'),
            Column::computed('action')
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'CustomerLifting_' . date('YmdHis');
    }
}
