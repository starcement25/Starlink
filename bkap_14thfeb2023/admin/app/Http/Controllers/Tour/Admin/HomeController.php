<?php

namespace App\Http\Controllers\Tour\Admin;

use App\Models\PageData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home()
    {
        return view('tour.home') ;
     
    }
    public function showRegistrations(Request $REQUEST)
    {
        $datas = PageData::where('page_id', 1)->get(['page_data']);
        $records = [] ;
        $allValues = [] ;
        foreach ($datas as $key => $data) {
            $records[] = json_decode($data->page_data, true) ;
        }
        $array =[];
        // $records = collect($records) ;
        // return $records ;
        foreach ($records as $key => $record) {
            $arrayKeys = array_keys($record);
            $allValues [] = array_values($record) ;
            foreach ($arrayKeys as $key => $itemKey) {
                $array[$itemKey][] = $record[$itemKey];
            }
        }
        $headers = array_keys($array);
        foreach ($allValues as $i => $items) {
          foreach ($items as $j => $item) {
            if(is_array($item)){
                $items[$j] = implode(',', $item) ;
            // $allValues[$i][$item[$j]] = implode(',', $item);
            }
             $allValues[$i] = $items ;
          }
        }
        //return  $allValues ;
        return view('tour.registration.index')->with('headers', $headers)->with('values', $allValues);
    }
}
