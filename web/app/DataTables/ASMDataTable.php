<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use App\Traits\HelperTrait;

class ASMDataTable extends DataTable
{
    use HelperTrait;
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('branch_name', function ($user) {
                return implode(', ', Branch::where('asm_user_id', $user->id)->pluck('name')->toArray()) ?? "";
            })
            ->addColumn('branch_code', function ($user) {
                return implode(', ',Branch::where('asm_user_id', $user->id)->pluck('branch_code')->toArray()) ?? "";
            })
            ->addColumn('action', function ($user) {
                return '<form method="POST" action="'.route('asm.destroy', ['asm'=> $user->id]).'">
                        '.csrf_field().'
                            <input name="_method" type="hidden" value="DELETE">
                            <div class="btn-group">
                            <a href="'.route('asm.edit', ['asm'=> $user->id]).'" class="btn btn-default btn-xs">
                                <i class="far fa-edit"></i>
                            </a>
                           <!--  <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                <i class="far fa-trash-alt"></i>
                            </button> -->
                        </div>';
            })
        ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->where('role', $this->getReservedRoleId('ASM'));
        // $loggedUser=Auth::user();
        // if($loggedUser->role > 6)
        // {
        //     $allocated_branches=json_decode($loggedUser->allocated_branches);
        //     return $model->newQuery()->with('mason_dealers')->with('te_linked')->with('states')->with(['branch', 'branch.zone'])->with('by_created')->where('role', 2)->whereIn('branch_id',$allocated_branches)->orderBy('id', 'DESC');
        //     // $temp = $model->newQuery()->with('mason_dealers')->with('te_linked')->with('states')->with('branch')->with('by_created')->where('role', 2)->whereIn('branch_id',$allocated_branches)->orderBy('id', 'DESC');
        //     // return Datatables::of($temp)->make(true);
        // }
        // else
        // {
        //     return $model->newQuery()->with('mason_dealers')->with('te_linked')->with('states')->with(['branch', 'branch.zone'])->with('by_created')->where('role', 2)->orderBy('id', 'DESC');
        //     // $temp = $model->newQuery()->with('mason_dealers')->with('te_linked')->with('states')->with('branch')->with('by_created')->where('role', 2)->orderBy('id', 'DESC');
        //     // return Datatables::of($temp)->make(true);
        // }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
        ->columns($this->getColumns())
        ->minifiedAjax()
      //  ->addAction(['width' => '120px', 'printable' => false])
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

            Column::make('id')->searchable(false)->visible(false),
            Column::make('name'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('branch_code')->title('Branch Code'),
            Column::make('branch_name')->title('Branch Name'),
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
        return 'ASM_' . date('YmdHis');
    }
}
