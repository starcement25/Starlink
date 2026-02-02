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
                    ->editColumn('user.by_created.name', function(UserCatalogueRedeemtion $data){
                        return $data->user->by_created->name ?? "" ;
                    })
        			->editColumn('user.branch.name', function (UserCatalogueRedeemtion $data) {
                       return $data->user->branch->name ?? "";
           
              		})
                    ->editColumn('delivery_date', function(UserCatalogueRedeemtion $data){
                        return isset($data->delivery_date) ? date('d-m-Y', strtotime($data->delivery_date)) : "" ;
                    })
                    ->editColumn('status', function(UserCatalogueRedeemtion $data){
                        $status = ['0'=> 'Pending','1' => 'Delivered', '3'=> 'Rejected'] ;
                        return $status[$data->status] ?? "" ;
                    })
                    ->editColumn('action', function(UserCatalogueRedeemtion $data){
                        return ' <div class="btn-group">
                        <a href="'.route('redeemtions.edit', ['redeemtion'=> $data->id]).'" class="btn btn-default btn-xs">
                            <i class="far fa-edit"></i>
                        </a>
                    </div>' ;
                    return $data->id;
                    })
                    ->rawColumns(['action']);
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
        ->with(['user','user.by_created', 'user.branch'])
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
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
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
            Column::make('id')->visible(false)->searchable(false),
            Column::make('user.name')->title('Mason Name'),
            Column::make('user.phone')->title('Mason Phone'),
            Column::make('user.by_created.name')->title('Employee Name'),
            Column::make('user.by_created.emp_code')->title('Employee Id'),
            Column::make('user.branch.name')->title('Branch'),
            Column::make('user.address')->title('Address'),
            Column::make('catalogue.name')->title('Catalogue'),
            Column::make('status'),
            Column::make('delivery_date'),
            Column::make('action'),
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
