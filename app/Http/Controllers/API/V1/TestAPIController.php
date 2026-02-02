<?php

namespace App\Http\Controllers\API\V1;

use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class TestAPIController extends Controller
{

    use HelperTrait;
    public function userPoint($userId) {
        return $this->calculatePoint($userId);
    }

   
}

