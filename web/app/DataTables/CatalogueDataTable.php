<?php

namespace App\DataTables;

use App\Models\Catalogue;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CatalogueDataTable extends DataTable
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
                    ->editColumn('mason_category.name', function ($catalogue) {
                        return $catalogue->mason_category->name ?? "" ;
                    })
                    ->editColumn('catalogueType.name', function ($catalogue) {
                        return $catalogue->catalogueType->name ?? "" ;
                    })
                    ->editColumn('created_at', function ($catalogue) {
                        return $catalogue->created_at->toDateTimeString() ?? "" ;
                    })
                    ->editColumn('updated_at', function ($catalogue) {
                        return $catalogue->created_at->toDateTimeString() ?? "" ;
                    })
                    ->editColumn('description', function ($catalogue) {
                        return stripslashes($catalogue->description) ?? "" ;
                    })
                    ->addColumn('image', function ($catalogue) {
                        return is_file($catalogue->image) ? '<img src="'.url("".$catalogue->image).'" width="50" height="50">'
                              : '<img src="'.url("default.jpg").'" width="50" height="50">'
                          ;
                    })
                    ->editColumn('status', function ($branch) {
                        $class = $branch->status == 1 ? 'badge-success' : 'badge-danger' ;
                        $text  = $branch->status == 1 ? 'Active' : 'Disabled' ;
                       
                        return '<span class="badge '.$class.'">'.$text.'</span>';
                    })
                    ->addColumn('action', function ($catalogue) {
                        return '<form method="POST" action="'.route('catalogues.destroy', ['catalogue'=> $catalogue->id]).'">
                                   '.csrf_field().'
                                    <input name="_method" type="hidden" value="DELETE">
                                    <div class="btn-group">
                                    <a href="'.route('catalogues.edit', ['catalogue'=> $catalogue->id]).'" class="btn btn-default btn-xs">
                                        <i class="far fa-edit"></i>
                                    </a>
                                  <!--  <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                        <i class="far fa-trash-alt"></i>
                                    </button>-->
                                </div>
                                </form>';
                     })
                    ->rawColumns(['action', 'image', 'status']);
    
        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Catalogue $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Catalogue $model): QueryBuilder
    {
        return $model->newQuery()->with(['mason_category', "catalogueType"])->where("status", Catalogue::STATUS_ACTIVE)->orderBy('status', 'DESC')->orderBy('id', 'DESC');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('catalogue-table')
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
         
            Column::make('id')->title('id')->visible(false)->searchable(false),
            Column::make('image')->searchable(false),
            Column::make('name'),
            Column::make('mason_category.name')->title('Contractor Category'),
            Column::make('catalogueType.name')->title('Type'),
            Column::make('catalogue_code')->title('Code'),
            Column::make('description'),
            Column::make('point'),
            Column::make('status'),
            Column::make('created_at'),
            Column::make('updated_at'),
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
        return 'Catalogue_' . date('YmdHis');
    }
}
