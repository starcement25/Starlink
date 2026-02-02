<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
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
            ->editColumn('reward_point.point', function($product) {
                return $product->reward_point->point ?? "";
            })
            ->editColumn('reward_point.bag', function($product) {
                return $product->reward_point->bag ?? "";
            })
            ->addColumn('action', function ($product) {
                        return '<form method="POST" action="'.route('products.destroy', ['product'=> $product->id]).'">
                                   '.csrf_field().'
                                    <input name="_method" type="hidden" value="DELETE">
                                    <div class="btn-group">
                                    <a href="'.route('products.edit', ['product'=> $product->id]).'" class="btn btn-default btn-xs">
                                        <i class="far fa-edit"></i>
                                    </a>
                                   <!-- <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                        <i class="far fa-trash-alt"></i>
                                    </button> -->
                                </div>';
                     })
            ->setRowId('id', function($product){
                return "row-".$product->id ;
            })
            ->editColumn('status',function($product){
                $class = $product->status == 1 ? 'badge-success' : 'badge-danger' ;
                $text  = $product->status == 1 ? 'Active' : 'Deactive' ;
                    
                return '<span class="badge '.$class.'">'.$text.'</span>';
            })
            ->rawColumns(['action','status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery()->with('reward_point');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('products-table')
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
            Column::make('id'),
            Column::make('name'),
            Column::make('description'),
            Column::make('reward_point.bag')->title('Bag'),
            Column::make('reward_point.point')->title('Reward Point'),
            Column::make('bonus_points'),
            Column::make('more_than_bags'),
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
        return 'Products_' . date('YmdHis');
    }
}
