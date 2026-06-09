<?php

namespace App\DataTables;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BannerDataTable extends DataTable
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
        ->editColumn('img', function ($banner) {
            return !empty($banner->img) ? '<img src="'.url("".$banner->img).'" width="50" height="50">'
                : '<img src="'.url("default.jpg").'" width="50" height="50">'
            ;
        })
        ->editColumn('status', function ($banner) {
            $class = $banner->status == 1 ? 'badge-success' : 'badge-danger' ;
            $text  = $banner->status == 1 ? 'Active' : 'Disabled' ;
        
            return '<span class="badge '.$class.'">'.$text.'</span>';
        })
        ->editColumn('status', function ($branch) {
            $class = $branch->status == 1 ? 'badge-success' : 'badge-danger' ;
            $text  = $branch->status == 1 ? 'Active' : 'Disabled' ;
           
            return '<span class="badge '.$class.'">'.$text.'</span>';
        })
        ->addColumn('action', function ($banner) {
            return '<form method="POST" action="'.route('banners.destroy', ['banner'=> $banner->id]).'">
                       '.csrf_field().'
                        <input name="_method" type="hidden" value="DELETE">
                        <div class="btn-group">
                         <a href="'.route('banners.edit', ['banner'=> $banner->id]).'" class="btn btn-default btn-xs">
                            <i class="far fa-edit"></i>
                        </a> 
                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                    </form>';
         })
        ->addColumn('zones', function ($banner) {
                return $banner->zones->pluck('name')->implode(', ');
            })
        ->rawColumns(['img', 'action', 'status'])
        ;


    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Banner $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Banner $model): QueryBuilder
    {
        return $model->newQuery()->with('zones');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('banner-table')
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

            
            Column::make('title'),
            Column::make('description'),
            Column::computed('zones')
            ->title('Zones')
            ->exportable(false)
            ->printable(true),

            Column::make('img')->title('Image')->exportable(false),
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
        return 'Banner_' . date('YmdHis');
    }
}
