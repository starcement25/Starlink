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
use Illuminate\Support\Facades\Auth;

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
                    // ->editColumn('mason_user.user.name', function($lifting) {
                    //     return $lifting->mason_user->user->name ?? "";
                    // })
                    // ->addColumn('image', function ($lifting) {
                    //     return !empty($lifting->img) ? '<img src="'.url("".$lifting->img).'" width="50" height="50">'
                    //           : '<img src="'.url("default.jpg").'" width="50" height="50">'
                    //       ;
                    // })
                    ->addColumn('action', function ($lifting) {
                        // if($lifting->reward->point > $lifting->user->points){

                        // }
                        $netPoint=$lifting->mason_user->user->points ?? 0;
                        $liftingPoint=0;
                        foreach($lifting->reward as $val)
                        {
                            $liftingPoint+=$val->point;
                        }
                        //$liftingPoint=$lifting->reward[0]->point ?? 0;
                        if($netPoint >= $liftingPoint)
                        {
                            return '<form method="POST" action="'.route('liftings.destroy', ['lifting'=> $lifting->id]).'">
                            '.csrf_field().'
                             <input name="_method" type="hidden" value="DELETE">
                             <div class="btn-group">
                             <a href="'.route('liftings.edit', ['lifting'=> $lifting->id]).'" class="btn btn-default btn-xs">
                                 <i class="far fa-edit"></i>
                             </a>
                             <span>
                                 <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                     <i class="far fa-trash-alt"></i>
                                 </button>
                             </span>
                         </div>';
                            
                        }
                        
                      
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
        $loggedUser=Auth::user();
        if($loggedUser->id > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            return $model->newQuery()->whereHas('user', function ($query) use($allocated_branches){
                $query->whereIn('branch_id',$allocated_branches);
            })->with(['product' => function($q){
                $q->select('id', 'name');
            }])->with(['user'=> function($query){
                $query->select('id', 'name','emp_code');
            }])->with(['mason_user','mason_user.user','mason_user.user.branch'])
            ->orderBy('id', 'DESC');
        }
        else
        {
            return $model->newQuery()->with(['product' => function($q){
                $q->select('id', 'name');
            }])->with(['user'=> function($query){
                $query->select('id', 'name','emp_code');
            }])->with(['mason_user','mason_user.user','mason_user.user.branch'])
            ->orderBy('id', 'DESC');
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
                    ->setTableId('lifting-table')
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
            Column::make('user.emp_code')->title('Dealer Code'),
            Column::make('user.name')->title('Dealer'),
            Column::make('mason_user.user.name')->title('Mason'),
            Column::make('mason_user.user.phone')->title('Mason Mobile'),
            Column::make('mason_user.user.branch.name')->title('Mason Branch'),
            Column::make('product.name'),
            Column::make('qty'),
            Column::make('remark'),
            // Column::make('image')->searchable(false),
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
