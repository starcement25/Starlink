<?php

namespace App\DataTables;

use App\Models\Lifting;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class LiftingDataTable extends DataTable
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
                    ->editColumn('product.name', function($lifting) {
                        return $lifting->product->name ?? "";
                    })
                    ->editColumn('user.name', function($lifting) {
                        return $lifting->user->name ?? "";
                    })
                    ->editColumn('mason_user.user.name', function($lifting) {
                        return $lifting->mason_user->user->name ?? "";
                    })
                    ->addColumn('image', function ($lifting) {
                        return !empty($lifting->img) ? '<img src="'.url("".$lifting->img).'" width="50" height="50">'
                              : '<img src="'.url("default.jpg").'" width="50" height="50">'
                          ;
                    })
                    ->addColumn('action', function ($lifting) {
                        return '<form method="POST" action="'.route('liftings.destroy', ['lifting'=> $lifting->id]).'">
                                   '.csrf_field().'
                                    <input name="_method" type="hidden" value="DELETE">
                                    <div class="btn-group">
                                    <a href="'.route('liftings.edit', ['lifting'=> $lifting->id]).'" class="btn btn-default btn-xs">
                                        <i class="far fa-edit"></i>
                                    </a>
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>';
                     })
                    ->rawColumns(['action', 'image']);
    
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
        return $model->newQuery()->with(['product' => function($q){
            $q->select('id', 'name');
        }])->with(['user'=> function($query){
            $query->select('id', 'name');
        }])->with(['mason_user','mason_user.user'])
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
        // return [
        //     [
        //         'data' => 'id',
        //         'title' => 'Id',
        //         'visible'=>false,

        //     ],
        //     [
        //         'data' => 'lifting_date',
        //         'title' => 'Date',

        //     ],
        //     [
        //         'data' => 'user.name',
        //         'title' => 'User',

        //     ],
        //     [
        //         'data' => 'product.name',
        //         'title' => 'Product',

        //     ],
        //     [
        //         'data' => 'qty',
        //         'title' => 'Quantity',

        //     ],
        //     [
        //         'data' => 'remark',
        //         'title' => 'Remark',

        //     ],
        //     [
        //         'data' => 'action',
        //         'title' => 'Action',
        //         'searchable' => false,

        //     ],
            
        // ];
        return [
           
            Column::make('id')->title('id')->visible(false)->searchable(false),
            Column::make('lifting_date'),
            Column::make('user.name'),
            Column::make('mason_user.user.name')->title('Mason'),
            Column::make('product.name'),
            Column::make('qty'),
            Column::make('remark'),
            Column::make('image')->searchable(false),
            Column::computed('action')
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
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
        return 'Lifting_' . date('YmdHis');
    }
}
