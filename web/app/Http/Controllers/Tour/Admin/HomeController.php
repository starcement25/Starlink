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
        $allKeys = [] ; 
        $kk = [] ;
        foreach ($datas as $key => $data) {
            $records[] = json_decode($data->page_data, true) ;
        }
        $array =[];
        // $records = collect($records) ;
        // return $records ;

        // Gathering All Keys From json
        foreach ($records as $key => $record) {
            $arrayKeys = array_keys($record);
            foreach ($arrayKeys as $key => $dataKey) {
               if(!in_array($dataKey, $allKeys)){
                    $allKeys[] = $dataKey ;
               }
            }
        }
        //return $allKeys ;

        // Gathering Json keys values To Specific key.
        // foreach ($records as $key => $record) {
        //     $arrayKeys = array_keys($record);
        //    // $allValues [] = array_values($record) ;
        //     foreach ($allKeys as $key => $itemKey) {
        //         $array[$itemKey][] = $record[$itemKey] ?? null;
        //         if(!array_key_exists($itemKey, $record)){
        //             $record[$itemKey] = null;
        //         }
        //     }
        //     $allValues [] = array_values($record) ;
        // }
        // $collectionArray = collect($array) ;
     
        
        // Gathering All Json values To array.
         foreach ($records as $key => $record) {
          //  $arrayKeys = array_keys($record);
            $arrayValue = [] ;
           // $allValues [] = array_values($record) ;
            foreach ($allKeys as $key => $itemKey) {
                $arrayValue[] = $record[$itemKey] ?? null;
                
            }
            $allValues[] = $arrayValue ;
           // $allValues [] = array_values($record) ;
        }

       
       // $headers = array_keys($array);
      //  return $allKeys ;
        foreach ($allValues as $i => $items) {
          foreach ($items as $j => $item) {
            if(is_array($item)){
                $items[$j] = implode(',', $item) ;
            // $allValues[$i][$item[$j]] = implode(',', $item);
            }
             $allValues[$i] = $items ;
          }
        }
       // return $allValues ;
        return view('tour.registration.index')->with('headers', $allKeys)->with('values', $allValues);
    }
}
