<?php

namespace App\Http\Controllers\Debug;

use App\Models\User;
use App\Models\LiftingApprovalHistory;
use App\Models\Reward;
use App\Models\Lifting;
use App\Models\Product;
use App\Models\Log;
use App\Models\RewardHistory;
use App\Models\UserCatalogueRedeemtion;
use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use App\Models\MasonLifting;
use App\Models\CustomerLifting;
use App\Exports\LiftingExport;
use Illuminate\Http\Request;
use App\Exports\VerifyLiftingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\DataTables\LiftingDataTable;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Lifting\CreateLiftingRequest;
use App\Http\Requests\Lifting\UpdateLiftingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
// use Illuminate\Support\Facades\Session;

class LiftingDebugController extends Controller
{
    use HelperTrait;
  

    public function testDebug(Request $request)  {
        return "Hi";
    }

   
}
