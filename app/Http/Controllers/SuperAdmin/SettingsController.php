<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Settings;
class SettingsController extends Controller
{
    function settings()
    {
        $sets = Settings::get();
        $settings = array();
        foreach($sets as $set)
        {
          $settings[$set->name] = $set->value;
        }
        $settings = (object)$settings;
        return view('superadmin/settings');
    }
}
