<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class UserDataTable extends DataTable
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
        ->editColumn('branch.name', function ($user) {
                $branchIds=json_decode($user->allocated_branches);
                $branches="";
                $i=0;
                if($branchIds != null)
                {
                    foreach($branchIds as $val)
                    {
                        if($i!=0)
                        {
                            $branches.=", "; 
                        }
                        $temp=Branch::where('id',$val)->pluck('name');
                        $branches.=$temp[0];
                        $i++;
                    }
                    return $branches;
                }
                return "";
        })
        ->editColumn('roles.role_name', function ($user) {
            return $user->roles->role_name ?? "";
        })
        ->editColumn('status', function ($branch) {
            $class = $branch->status == 1 ? 'badge-success' : 'badge-danger' ;
            $text  = $branch->status == 1 ? 'Active' : 'Disabled' ;
           
            return '<span class="badge '.$class.'">'.$text.'</span>';
        })
        ->addColumn('action', function ($user) {
            return '<form method="POST" action="'.route('users.destroy', ['user'=> $user->id]).'">
                       '.csrf_field().'
                        <input name="_method" type="hidden" value="DELETE">
                        <div class="btn-group">
                        <a href="'.route('users.edit', ['user'=> $user->id]).'" class="btn btn-default btn-xs">
                            <i class="far fa-edit"></i>
                        </a>'.
                        // <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                        //     <i class="far fa-trash-alt"></i>
                        // </button>
                    '</div>';
         })
         ->setRowId('id')
         ->rawColumns(['status','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->with(['branch', 'roles'])->whereHas('roles',function($q){
            $q->where('is_reserved_role',0);
        })->orderBy('id', 'DESC');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('user-table')
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
            Column::make('name'),
            Column::make('email'),
            Column::make('roles.role_name')->title("Role"),
            Column::make('phone'),
            Column::make('branch.name'),
            Column::make('status'),
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
        return 'User_' . date('YmdHis');
    }
}
