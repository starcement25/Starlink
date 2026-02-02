<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\MasonPointReportDataTable;

class ReportController extends Controller
{
    public function masonReports(MasonPointReportDataTable $dataTable, Request $request)
    {
        $users = User::where('role', 2)->get() ;
        return view('admin.user.mason-point')->with('users', $users);
        //return $users ;
    }
}
