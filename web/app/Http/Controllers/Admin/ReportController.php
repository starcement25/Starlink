<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\MasonPointReportDataTable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasonPointExport;

class ReportController extends Controller
{
    public function masonReports(MasonPointReportDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('reports.view') ;
        $users = User::where('role', 2)->paginate(100) ;
        return view('admin.user.mason-point')->with('users', $users);
        //return $users ;
    }

    public function masonReportsExport() 
    {
        set_time_limit(0);
        return Excel::download(new MasonPointExport, 'MasonPoints.xlsx');
        //return (new CustomerLiftingExport)->store('CustomerLifting.xlsx');
    }
}
