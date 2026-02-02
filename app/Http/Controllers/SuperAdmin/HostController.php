<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\user_master;
use App\Model\host_location;
use App\Model\host_amenities;
use App\Model\LocationImage;
class HostController extends Controller
{
    function index()
    {
        $users = user_master::where('role','2')->get();
        return view('superadmin/hosts')->with(['users'=>$users]);
    }
    function hostLocation($user_id)
    {
        $host_location = host_location::where('user_id',$user_id)->get();
        $user = user_master::where('id',$user_id)->first();
        return view('superadmin/host-location')->with(['locations' => $host_location,'user' => $user]);
    }
    function hostLocationDetail($location_id)
    {
        $readonly = "readonly";
        $host_location = host_location::where('id',$location_id)->first();   
        return view('superadmin/host-location-detail')->with(['location' => $host_location,'location_id'=> $location_id,'readonly' => $readonly]);
    }
    function hostLocationDelete($location_id)
    {
        $qry = host_location::where('id', $location_id)->first(); 
        $amen = host_amenities::where('location_id',$location_id)->first();
        $user_id = $qry->user_id;
    	$qry->delete();
        $amen->delete();
        session()->flash('success','Deleted Successfully');
    	return redirect()->route('superadmin.host-location',$user_id);
    }
    function addHostLocation()
    {
    	$location_id ="";
        $readonly = " ";
        $users = user_master::where('role', 2)->get(); 
        return view('superadmin/host-location-detail')->with(['location_id'=> $location_id,'users'=>$users,'readonly' => $readonly]);
    }
    function hostLocationDetailsSave(Request $req){
        
        if($req->user_id == 0)
        {
            $id = userdata()->id;
        }else
        {
           $id = $req->user_id;
        }
        
        if(is_null($req->location_id))
        {
         
         $location = new host_location;
         $amen =  new host_amenities;
 
        }
        else
        {
         $location = host_location::find($req->location_id);
         $amen = host_amenities::where('location_id',$req->location_id)->first();
        }
        
         $location->user_id = $id;
         $location->address =$req->address;
         $location->city = $req->city;
         $location->country = $req->country;
         $location->state = $req->state;
         $location->zip_code  = $req->zipcode;
         $location->location_name = $req->location_name;
         $location->no_of_spot = $req->no_of_spot;
         $location->max_rv_lenght = $req->max_rv_lenght;
         $location->tow_parking_detail = $req->tow_parking_detail;
         $location->road_condition = $req->road_condition;
         $location->local_attraction = $req->local_attraction;
         $location->max_stay = $req->max_stay;
         $location->min_req_notice = $req->min_req_notice;
         $location->direction_details = $req->direction_details;
         $location->save();
         $amen->location_id = $location->id;
         $amen->space = $req->max_rv_lenght;
         $amen->size = $req->size;
         $amen->stay = $req->max_stay;
         $amen->price_per_night = $req->price_per_night;
         if(!is_null($req->from_date) && !is_null($req->to_date))
         {
             $from_date = $req->from_date;
             $to_date = $req->to_date;
             foreach($from_date as $key => $from)
             {
                 
                 if($req->d_ids[$key] == 0)
                 {
                     $tbl = new host_booked_date;
                 }else
                 {
                     $tbl = host_booked_date::where('id',$req->d_ids[$key])->first();
                 }
                 $tbl->from_date = $from_date[$key];
                 $tbl->to_date = $to_date[$key];
                 $tbl->location_id = $location->id;
                 $tbl->save();
                 
             }
         }
 
         if ($req->hasFile('images')) {
             foreach($req->file('images')  as $key=>$file)
             {
                 $image = new LocationImage();
                 $num = $key+1;
                 $extention = $file->getClientOriginalExtension();
                 $filename = $location->id.'-'.$num.'.jpg';
                 $path = public_path('/img/locations/');
                 $file->move($path, $filename);
                 $image = LocationImage::where('img',$filename)->first();
                 if(!$image)
                 {
                     $image = new LocationImage;
                 }
                 $image->location_id = $location->id;
                 $image->img = $filename;
                 $image->save();
             }
 
         }
         if( $req->has('tow_parking_available') ){
             $location->tow_parking_available = $req->tow_parking_available;
         }
         else
         {
             $location->tow_parking_available = 0;
         }
 
         if( $req->has('pull_through_parking_available') ){
             $location->pull_through_parking_available = $req->pull_through_parking_available;
         }
         else
         {
             $location->pull_through_parking_available = 0;
         }
         $location->save();
 
         if( $req->has('slideouts') ){
             $amen->slideouts = $req->slideouts;
         }else
         {
             $amen->slideouts = 0;
         }
 
         if( $req->has('generators') ){
             $amen->generators = $req->generators;
         }
         else
         {
             $amen->generators = 0;
         }
         if( $req->has('barbecues') ){
             $amen->barbecues = $req->barbecues;
         }
         else
         {
             $amen->barbecues = 0;
         }
        
         if( $req->has('lawnchairs') ){
             $amen->lawnchairs = $req->lawnchairs;
         }
         else
         {
             $amen->lawnchairs = 0;
         }
        
         if( $req->has('pets') ){
             $amen->pets = $req->pets;
         }
         else
         {
             $amen->pets  = 0;
         }
 
         if( $req->has('amp_electric_15') ){
             $amen->amp_electric_15 = $req->amp_electric_15;
         }
         else
         {
             $amen->amp_electric_15 = 0;
         }
         if( $req->has('amp_electric_30') ){
             $amen->amp_electric_30 = $req->amp_electric_30;
         }
         else
         {
             $amen->amp_electric_30 = 0;
         }
         
         
         if( $req->has('amp_electric_50') ){
             $amen->amp_electric_50 = $req->amp_electric_50;
         }
         else
         {
             $amen->amp_electric_50 = 0;
         }
 
         if( $req->has('sewer_dump') ){
             $amen->sewer_dump = $req->sewer_dump;
         }
         else
         {
             $amen->sewer_dump = 0;
         }
         
 
         if( $req->has('full_hook_ups') ){
             $amen->full_hook_ups = $req->full_hook_ups;
         }
         else
         {
             $amen->full_hook_ups = 0;
         }
         
         if( $req->has('sewer_water') ){
             $amen->sewer_water = $req->sewer_water;
         }
         else
         {
             $amen->sewer_water = 0;
         }
 
 
         if( $req->has('tow_vehicle_parking') ){
             $amen->tow_vehicle_parking = $req->tow_vehicle_parking;
         }
         else
         {
             $amen->tow_vehicle_parking = 0;
         }
         
         if( $req->has('wifi') ){
             $amen->wifi = $req->wifi;
         }
         else
         {
             $amen->wifi = 0;
         }
 
         if($amen->save())
         {
             $req->session()->flash('success', 'Saved Successfully!');
         }else
         {
             $req->session()->flash(['error' => 'Something Wrong']);
         }
         
         $location_id = "";
         return redirect()->route('superadmin.add-location')->with(['location_id',$location_id]);
     }
}
