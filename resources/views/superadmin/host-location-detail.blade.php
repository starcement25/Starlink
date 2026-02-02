@extends('superadmin.layer')
@section('css_style')
<style>
     .suitability ul {
  list-style-type: none;
}

.suitability li {
  display: inline-block;
  color:black;
  font-weight:bold;
}

.suitability input[type="checkbox"][id^="cb"] {
  display: none;
}

.suitability label {
  border: 1px solid #fff;
  padding: 10px;
  display: block;
  position: relative;
  margin: 10px;
  cursor: pointer;
}

.suitability label:before {
  background-color: white;
  color: white;
  content: " ";
  display: block;
  border-radius: 50%;
  border: 1px solid grey;
  position: absolute;
  top: -5px;
  left: -5px;
  width: 25px;
  height: 25px;
  text-align: center;
  line-height: 28px;
  transition-duration: 0.4s;
  transform: scale(0);
}

.suitability label img {
  height: 80px;
  width: 80px;
  transition-duration: 0.2s;
  transform-origin: 50% 50%;
  filter: grayscale(100%);
}

.suitability :checked + label {
  border-color: #ddd;
}

.suitability :checked + label:before {
  content: "✓";
  background-color: #F15A22;
  transform: scale(1);
}


.suitability :checked + label img {
  transform: scale(0.9);
  filter: grayscale(0%);
  box-shadow: 0 0 5px #333;
  z-index: -1;
}
#map{
    position: relative;
    
}
#floating-panel{
    position:absolute;
}
.hostLocationSec h2 {
            font-size: 17px;
            font-weight: 600;
        }

        .hostLocationSec h2::first-letter {
            color: #F15A22;
        }

        .hostLocationSec .upload_block {
            width: 100%;
            height: 146px;
            border: 1px solid #ddd;
            padding: 4px;
            position: relative;
            overflow: hidden;
        }

        .hostLocationSec .upload_block>div {
            background: #f5f5f5;
            width: 100%;
            height: 100%;
            position: relative;
            display: flex;
            vertical-align: middle;
            align-items: center;
            justify-content: center;
        }

        .hostLocationSec .upload_block>div img {
            width: 60px;
            margin-right: 24px;
            object-fit: cover;
            object-position: top;
        }

        .hostLocationSec .upload_block>div h5 {
            color: #ff5a00;
            text-transform: inherit;
            font-weight: 500;
            font-family: 'ProximaNova Regular', sans-serif;
        }

        .hostLocationSec .upload_block>div span {
            font-size: 16px;
            color: #aaa;
            margin-left: 14px;
            font-weight: 500;
            font-family: 'ProximaNova Regular', sans-serif;
        }

        .hostLocationSec input#files {
            display: block;
        }

        .hostLocationSec  .upload_block>div input {
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
            opacity: 0;
            font-size: 0;
        }

        .hostLocationSec .trash {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 1;
        }

        .hostLocationSec  .uploadBx .imgBx {
            position: relative;
        }

        .hostLocationSec .uploadBx .imgBx i {
            background-color: #ff5a00;
            width: 25px;
            height: 25px;
            line-height: 25px;
            border-radius: 50%;
            text-align: center;
            color: #fff;
            font-size: 11px;
        }

      .hostLocationSec input::-webkit-input-placeholder, textarea::-webkit-input-placeholder  { /* Chrome/Opera/Safari */
			color:#a9a9a9;
			font-size: 16px;
		  }
		.hostLocationSec input::-moz-placeholder,textarea::-moz-placeholder { /* Firefox 19+ */
			color:#a9a9a9;
			font-size: 16px;
		  }
          .hostLocationSec input:-ms-input-placeholder, textarea:-ms-input-placeholder { /* IE 10+ */
			color:#a9a9a9;
			font-size: 16px;
		  }
		  .hostLocationSec input:-moz-placeholder, textarea:-moz-placeholder { /* Firefox 18- */
			color:#a9a9a9;
			font-size: 16px;
		  }

          /*--31-05-2022--*/
          .ui-widget.ui-widget-content {
                min-width:100%;
        }
        .startEnd{
            display: flex;
            justify-content: space-between;
        }
        .ui-datepicker {
    width: 100%;
    padding: .2em .2em 0;
    display: none;
}
.ui-datepicker .ui-datepicker-header {
    position: relative;
    padding: .2em 0;
    background: #F15A22;
}
.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active {
    border: 1px solid transparent;
    background: transparent;
    font-weight: normal;
    color: #F15A22;
}
.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight {
    border: 1px solid #F15A22;
    background: #F15A22;
    color: #000;
}
.ui-widget-content {
    border: 1px solid #ddd;
    background: rgba(255,255,255,0.4);
    color: #F15A22;
}


</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        
                </div>      
            </div>
        </div>
    </div>
       <!---->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                <br>
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {!! session()->get('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form  class="main-form hostLocationSec"  id="addLocation" method="post" action="{{route('superadmin.save-location')}}"  enctype="multipart/form-data">
                        @csrf
                        <input name="location_id" id="location-id" type="hidden" value="{{ $location_id }}" />
                      
                        <div class=>
                                    <label for="" class="form-label">User</label>
                                    @if(empty($location_id))
                                    <select class="form-control" name="user_id">
                                        <option value="0" selected>Admin</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}" >{{$user->user_fn}}({{$user->id}})</option>
                                        @endforeach
                                    </select>
                                    @else 
                                    <input class="form-control" type="text" id="" name="user_name" value="{{$location->user->user_fn}}" readonly>
                                    <input type="hidden" name="user_id" value="{{$location->user_id}}">
                                    @endif
                                    
                        </div>
                        
                        <div class="p-4 shadow-sm mb-4">
                                <h2><span>1</span> Location</h2>
                                <div class="mb-3">
                                    <label for="" class="form-label">Location name</label>
                                    <input class="form-control" type="text" id="" name="location_name" value="{{ $location->location_name ?? '' }}" {{ $readonly ?? '' }}>
                                </div>
                        </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>2</span> Location Details</h2>
                                
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label for="" class="form-label">Number of spots</label>
                                        <input class="form-control" type="text" id="" name="no_of_spot" value="{{ $location->no_of_spot ?? '' }}" {{ $readonly ?? '' }}>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="" class="form-label">Maximum RV length*</label>
                                        <input class="form-control" type="number" id="" name="max_rv_lenght" value="{{ $location->max_rv_lenght ?? '' }}" {{ $readonly ?? '' }}>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="" class="form-label">Size in fit</label>
                                        <input class="form-control" type="number" id="" name="size" value="{{ $location->amenities->size ?? '' }}" {{ $readonly ?? '' }}>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="" class="form-label">Tow vehicle parking available*</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tow_parking_available"
                                                    id="inlineRadio1" value="1" {!! !empty($location->tow_parking_available) ? 'checked' : '' !!} {{ $readonly ?? '' }} >
                                                <label class="form-check-label" for="inlineRadio1">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tow_parking_available"
                                                    id="inlineRadio2" value="0" {!! !empty($location->tow_parking_available) ? '' : 'checked' !!} {{ $readonly ?? '' }}>
                                                <label class="form-check-label" for="inlineRadio2">No</label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="" class="form-label">Pull-through parking available*</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="pull_through_parking_available"
                                                    id="inlineRadio3" value="1" {!! !empty($location->pull_through_parking_available) ? 'checked' : '' !!} {{ $readonly ?? '' }}>
                                                <label class="form-check-label" for="inlineRadio3">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="pull_through_parking_available"
                                                    id="inlineRadio4" value="0" {!! !empty($location->pull_through_parking_available) ? '' : 'checked' !!} {{ $readonly ?? '' }}>
                                                <label class="form-check-label" for="inlineRadio4">No</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-12">
                                        <label for="exampleFormControlTextarea1" class="form-label">Tow vehicle parking details</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="tow_parking_detail" rows="3" {{ $readonly ?? '' }}>
                                        {{ $location->tow_parking_detail ?? '' }}
                                        </textarea>
                                    </div>

                                    <div class="mb-3 col-md-12">
                                        <label for="exampleFormControlTextarea1" class="form-label">Access road conditions</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="road_condition" rows="3" {{ $readonly ?? '' }}>
                                        {{ $location->road_condition ?? '' }}
                                        </textarea>
                                    </div>

                                </div>

                            </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>3</span> Suitability</h2>
                                <p>Note: It is acceptable to request a nominal fee to cover costs if providing an
                                    electric hookup. If so, please note it and the amount in the House Rules section.
                                </p>

                                    <ul class="suitability">
                                        <li><center>Slideouts</center><input type="checkbox" id="cb1" name="slideouts"   value="1" {!! !empty($location->amenities->slideouts) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb1"><img src="{{URL::to('/')}}/public/web/images/slide_allow_red.svg"/></label>
                                        </li>
                                        <li><center>Generators</center><input type="checkbox" id="cb2" name="generators"  value="1" {!! !empty($location->amenities->generators) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb2"><img src="{{URL::to('/')}}/public/web/images/generator_allow_red.svg" /></label>
                                        </li>
                                        <li><center>Barbecues</center><input type="checkbox" id="cb3" name="barbecues"  value="1" {!! !empty($location->amenities->barbecues) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb3"><img src="{{URL::to('/')}}/public/web/images/slide_allow_red.svg"   /></label>
                                        </li>
                                        <li><center>Lawnchairs</center><input type="checkbox" id="cb4" name="lawnchairs" value="1"  {!! !empty($location->amenities->lawnchairs) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb4"><img src="{{URL::to('/')}}/public/web/images/lawnchairs_allow_red.svg" /></label>
                                        </li>
                                        <li><center>Pets</center><input type="checkbox" id="cb5" name="pets" value="1"  {!! !empty($location->amenities->pets) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb5"><img src="{{URL::to('/')}}/public/web/images/pet_welcom_red.svg" /></label>
                                        </li>
                                    
                                    </ul>
                            </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>4</span> Amenities</h2>
                                <p>Note: It is acceptable to request a nominal fee to cover costs if providing an
                                    electric hookup. If so, please note it and the amount in the House Rules section.
                                </p>

                                <ul class="suitability">
                                        <li><center>15 Amp Electric</center><input type="checkbox" id="cb6" name="amp_electric_15"  value="1" {!! !empty($location->amenities->amp_electric_15) ? 'checked' : '' !!} {{ $readonly ?? '' }}/>
                                            <label for="cb6"><img src="{{URL::to('/')}}/public/web/images/amp_icon_red.svg" /></label>
                                        </li>
                                        <li><center>30 Amp Electric</center><input type="checkbox" id="cb7" name="amp_electric_30"  value="1" {!! !empty($location->amenities->amp_electric_30) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb7"><img src="{{URL::to('/')}}/public/web/images/amp_icon_red.svg" /></label>
                                        </li>
                                        <li><center>50 Amp Electric</center><input type="checkbox" id="cb8" name="amp_electric_50"  value="1" {!! !empty($location->amenities->amp_electric_50) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb8"><img src="{{URL::to('/')}}/public/web/images/amp_icon_red.svg" /></label>
                                        </li>
                                        <li><center>Water Hookup</center><input type="checkbox" id="cb9" name="water_hookup"  value="1" {!! !empty($location->amenities->water_hookup) ? 'checked' : '' !!} {{ $readonly ?? '' }}/>
                                            <label for="cb9"><img src="{{URL::to('/')}}/public/web/images/water_icon.png" /></label
                                        </li>
                                        <li><center>Sewer Dump</center><input type="checkbox" id="cb10" name="sewer_dump"  value="1" {!! !empty($location->amenities->sewer_dump) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb10"><img src="{{URL::to('/')}}/public/web/images/dump.png" /></label>
                                        </li>
                                        <li><center>Full Hook ups</center><input type="checkbox" id="cb11" name="full_hook_ups"  value="1" {!! !empty($location->amenities->full_hook_ups) ? 'checked' : '' !!} {{ $readonly ?? '' }}/>
                                            <label for="cb11"><img src="{{URL::to('/')}}/public/web/images/Hookup_red.svg" /></label>
                                        </li>
                                        <li><center>Sewer Water</center><input type="checkbox" id="cb12" name="sewer_water"  value="1" {!! !empty($location->amenities->sewer_water) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb12"><img src="{{URL::to('/')}}/public/web/images/sewerwater.png" /></label>
                                        </li>
                                        <li><center>Wifi</center><input type="checkbox" id="cb13" name="wifi"  value="1" {!! !empty($location->amenities->wifi) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb13"><img src="{{URL::to('/')}}/public/web/images/wifi_icon_red.svg"  /></label>
                                        </li>
                                        <li><center>Tow Vehicle Parking</center><input type="checkbox" id="cb14" name="tow_vehicle_parking"  value="1" {!! !empty($location->amenities->tow_vehicle_parking) ? 'checked' : '' !!} {{ $readonly ?? '' }} />
                                            <label for="cb14"><img src="{{URL::to('/')}}/public/web/images/vehicle-red.svg" /></label>
                                        </li>
                                    
                                    </ul>
                            </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>5</span> General Description</h2>
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="general_description" rows="3" {{ $readonly ?? '' }}> {{ $location->amenities->general_description ?? '' }} 
                                        </textarea>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label for="exampleFormControlTextarea1" class="form-label">Suggested local
                                            attractions</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="local_attraction" rows="3" {{ $readonly ?? '' }}>
                                        {{ $location->local_attraction ?? '' }}
                                        </textarea>
                                    </div>
                                </div>

                            </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>6</span> Specify any house rules</h2>
                                <div class="row">

                                    <div class="mb-3 col-md-6">
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="6" name="house_rules" {{ $readonly ?? '' }}> {{ $location->amenities->house_rules ?? '' }}
                                        </textarea>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <h6><strong>For example:</strong></h6>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">No smoking on property</li>
                                            <li class="list-group-item">No children under the age of 3 please</li>
                                            <li class="list-group-item">No running generators after 10pm</li>
                                            <li class="list-group-item">We'd prefer you be gone in the morning before we
                                                head to work at 9am</li>
                                        </ul>
                                    </div>

                                </div>

                            </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>7</span> Your preferences</h2>
                                <div class="row">

                                    <div class="mb-3 col-md-4">
                                        <label for="" class="form-label">Max stay*(nights)</label>
                                        <input class="form-control" type="text" id="" name="max_stay" value="{{ $location->max_stay ?? '' }} " {{ $readonly ?? '' }}>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="" class="form-label">Minimum Request Notice Time</label>
                                        <input class="form-control" type="number" id="" name="min_req_notice" value="{{ $location->min_req_notice ?? '' }}" {{ $readonly ?? '' }}>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="" class="form-label">Maximum Request Notice Time</label>
                                        <input class="form-control" type="number" id="" name="max_req_notice" value="{{ $location->max_req_notice ?? '' }}" {{ $readonly ?? '' }}>
                                    </div>

                                </div>

                            </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>8</span> Photo of your host location</h2>

                                <div class="field upload_block">
                                    <div>
                                        <img
                                            src="https://iparkrv.com/wp-content/themes/iparkrv/assets/images/upload.svg">
                                        <h5>Upload Photo</h5><span>Maximum file size is 8MB.</span>
                                        <input type="file" id="files" name="images[]" multiple accept="image/*" {{ $readonly ?? '' }}>
                                    </div>
                                </div>

                                <section class="uploadBx">
                                    <label class="form-label">Favorite locations</label>

                                    <div class="row" id="gallery">
                                        @if(!empty($location_id))
                                            @foreach($location->images as $image)
                                            <div class="mb-3 col-md-3 ">
                                                <div class="shadow-sm p-2 h-100">
                                                <img src="{{URL::to('/')}}/public/img/locations/{{$image->img}}"style="height:200px" alt="">
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                       
                                    </div>

                                </section>
                                <hr>

                            </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>9</span> Address of your host location?</h2>
                                <p>Note: Your exact address will only be shared with guests after you have accepted
                                    their stay request. A generalized location is shown to members who you have not
                                    personally accepted as guests.</p>
                                <div class="row">

                                    <div class="mb-3 col-md-12">
                                        <label for="" class="form-label">Street Address</label>
                                        <input class="form-control" type="text" id="address" name="address"
                                            value="{{ $location->address ?? '' }}" {{ $readonly ?? '' }}>
                                    </div>

                                    
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="" value="{{ $location->city ?? '' }}" required {{ $readonly ?? '' }}>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="" value="{{ $location->state ?? '' }}"  required {{ $readonly ?? '' }}>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" name="country" placeholder="" value="{{ $location->country ?? '' }}" required {{ $readonly ?? '' }}>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="" value="{{ $location->zip_code ?? '' }}" required {{ $readonly ?? '' }}>
                                    </div>
                                </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>10</span> Location map</h2>
                                <p>Note: Your exact location and any detailed directions provided here will only be
                                    shared with guests after you have accepted their stay request.</p>
                                <div class="my-3">
                                    
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                    <div id="map" style="width:100%;height:200px">
                                        
                                    </div>
                                        <label for="" class="form-label">Direction details</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="direction_details" rows="4" {{ $readonly ?? '' }}> {{ $location->direction_details ?? '' }}  
                                        </textarea>
                                    </div>                                   
                                </div>

                            </div>

                            <div class="p-4 shadow-sm mb-4">
                                <h2><span>11</span> Availability Details & Price</h2>
                                <p>Please add any dates you wish to block. Guests will be unable to request to stay on those dates.</p>
                                <div class="row mb-3">
                                   
                                    <div class="col-md-12">
                                        

                                        <div id="startEnd">
                                            @if(!empty($location_id))
                                                @if(!$location->availibilities->isEmpty())
                                                    @foreach($location->availibilities as $avail)
                                                        
                                                        <div>
                                                            <div class="startEnd">
                                                                <div>
                                                                    <h6>Start Date</h6>
                                                                    {{ \Carbon\Carbon::parse($avail->from_date)->format('d/m/Y')}}<input type="hidden" value="{{$avail->from_date}}'" name="from_date[]"> 
                                                                </div>
                                                                <span class="input-group-text">To</span> 
                                                                <div>
                                                                    <h6>End Date</h6>
                                                                    {{ \Carbon\Carbon::parse($avail->to_date)->format('d/m/Y')}} <input type="hidden" value="{{$avail->to_date}}" name="to_date[]"> 
                                                                </div>
                                                                <span class="input-group-text"> <a  class="" onclick="deleteDate(this,{{$avail->id}})"> <i class="fas fa-trash-alt"></i> </a> </span>
                                                            </div>
                                                            <hr>
                                                            <input type="hidden" name="d_ids[]" value="{{$avail->id}}">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </div>
                                          
                                          <a href="" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            Add Blocked Date
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label for="" class="form-label">Price per night</label>
                                        <input class="form-control" type="text" id="" name="price_per_night" value="{{ $location->amenities->price_per_night ?? '' }}" {{ $readonly ?? '' }}>
                                    </div>
                                                                        
                                </div>                            
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label for="" class="form-label">Availability details</label>
                                        
                                        <div id="datepicker-2"></div>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="4" name="availability_detail" placeholder="Ex: You can use this space to tell potential guests about likelihood of availability that might not be in your calender, for example:We're at the cottage most weekends so this location is unavailable then, or We do a lot of last minute trips in the summer, but feel free to give us a shout and see if we can accommodate you." {{ $readonly ?? '' }}></textarea>
                                    </div>     
                                                                    
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                      
                                           @if(empty($location_id))
                                            <button type="submit" class="btn btn-success">Create Host Location </button>
                                           @endif
                                      
                                    </div>                                   
                                </div>

                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">ADD NOT AVAILABLE DATE</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Start Date</label>
                  <input type="date" class="form-control" id="fromDate" name="from_date[]">
                </div>
                <div class="mb-3">
                  <label for="" class="form-label">End Date</label>
                  <input type="date" class="form-control" id="toDate" name="to_date[]">
                </div>
                <button type="button" onClick="addDate()" class="btn btn-warning">Submit</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('js')
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key=AIzaSyDb93V-StIq4rlMJu9qXUYJRyf3g3qyxyc"></script>
<script type="text/javascript">
    

    function initialize(address) {
    var geocoder;
    var map;
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(-34.397, 150.644);
        var myOptions = {
            zoom: 8,
            center: latlng,
            mapTypeControl: true,
            mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
            },
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map"), myOptions);
        if (geocoder) {
            geocoder.geocode({
            'address': address
            }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                map.setCenter(results[0].geometry.location);

                var infowindow = new google.maps.InfoWindow({
                    content: '<b>' + address + '</b>',
                    size: new google.maps.Size(150, 50)
                });

                var marker = new google.maps.Marker({
                    draggable: true,
                    position: results[0].geometry.location,
                    map: map,
                    title: address
                });
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map, marker);
                });
                google.maps.event.addListener(marker, 'dragend', function(event) {
                            mymap(event.latLng.lat(),event.latLng.lng());
                });

                } else {
                alert("No results found");
                }
            } else {
                alert("Geocode was not successful for the following reason: " + status);
            }
            });
        }
    }
    function mymap(latitude,longitude,map,marker)
    {
        var latlng = new google.maps.LatLng(latitude, longitude);
            var geocoder = geocoder = new google.maps.Geocoder();
            
           
            geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    console.log(results[0]);
                    if (results[0]) {
                        var map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 8,
                            center: latlng
                        });
                        map.setZoom(11);
                        var marker = new google.maps.Marker({
                            draggable: true,
                            position: latlng,
                            map: map,
                            title: "Your location"
                        });
                        var infowindow = new google.maps.InfoWindow;
                        infowindow.setContent(results[0].formatted_address);
                        infowindow.open(map, marker);
                        var address = results[0].formatted_address;
                        var pin = results[0].address_components[results[0].address_components.length - 1].long_name;
                        var country = results[0].address_components[results[0].address_components.length - 2].long_name;
                        var state = results[0].address_components[results[0].address_components.length - 3].long_name;
                        var city = results[0].address_components[results[0].address_components.length - 4].long_name;
                        document.getElementById('country').value = country;
                        document.getElementById('state').value = state;
                        document.getElementById('city').value = city;
                        document.getElementById('zipcode').value = pin;
                        document.getElementById('address').value = address;

                        google.maps.event.addListener(marker, 'dragend', function(event) {
                            mymap(event.latLng.lat(),event.latLng.lng());
                        });
                    }
                }
            });
    }
   
    google.maps.event.addDomListener(window, 'load', function () {
        let myLatitude;
        let myLongitude;
        var address = "{{ $location->address ?? '' }}";
        if(address != '')
        {
            initialize(address);
        }else
        {
            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition(function(position){
                    myLatitude = parseFloat(position.coords.latitude);
                    myLongitude = parseFloat(position.coords.longitude);
                    var myLatlng = new google.maps.LatLng(myLatitude, myLongitude);
                    var myOptions = {
                        zoom: 8,
                        center: myLatlng,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                    var map = new google.maps.Map(document.getElementById("map"), myOptions);
                        var marker = new google.maps.Marker({
                            draggable: true,
                            position: myLatlng,
                            map: map,
                            title: "Your location"
                        });
                        google.maps.event.addListener(marker, 'dragend', function(event) {
                            mymap(event.latLng.lat(),event.latLng.lng());
                        });
                });
            }else{
                myLatitude = 40.713956;
                myLongitude = -74.006653;
            }
        }
    
        var places = new google.maps.places.Autocomplete(document.getElementById('address'));
        google.maps.event.addListener(places, 'place_changed', function () {
            var place = places.getPlace();
            var address = place.formatted_address;
            
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();
            mymap(latitude,longitude);
        });
    });
</script>
<script>
    function deleteDate(self,id)
    {
        if(id != 0)
        {
           if(confirm("Are you sure want to remove it?"))
           {
            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
              $.ajax({
                  url: "{{ route('admin.host-location-avail-delete') }}",
                  method: 'post',
                  data: {
                     id:id
                  },
                  success: function(result){
                     if(result['success'] == '1')
                     {
                        $(self).parent().parent().parent().remove(); 
                     }
                  }});
           }
        }else
        {
            $(self).parent().parent().parent().remove();
        }
        
    }
	function addDate()
    {
        
        var fromDate1 = $('#fromDate').val();
        var toDate1 = $('#toDate').val();
        

        

        if(fromDate1 != '' && toDate1 != '')
        {
            var fd = new Date(fromDate1);
            var fdt = fd.getDate();
            var fmn = fd.getMonth();
            fmn++;
            var fyy = fd.getFullYear();
            fromDate = fdt + "-" + fmn + "-" + fyy ;

            var td = new Date(toDate1);
            var tdt = td.getDate();
            var tmn = td.getMonth();
            tmn++;
            var tyy = td.getFullYear();
            toDate = tdt + "-" + tmn + "-" + tyy ;

            var html = '<div><div class="startEnd"> <div> <h6>Start Date</h6> '+fromDate+'<input type="hidden" value="'+fromDate1+'" name="from_date[]"> </div> <span class="input-group-text">To</span> <div> <h6>End Date</h6> '+toDate+' <input type="hidden" value="'+toDate1+'" name="to_date[]"> </div> <span class="input-group-text"> <a  class="" onclick="deleteDate(this,0)"> <i class="fa fa-trash"></i> </a> </span></div><hr><input type="hidden" name="d_ids[]" value="0"></div>';
            var startEndC = $('#startEnd').html();
            html = startEndC + html;
            $('#startEnd').html(html);
            $('#exampleModal').modal('hide');
        }else
        {
            alert('please select date');
        }
        
    }
   

		$.validator.setDefaults({
		submitHandler: function() {
			//alert("submitted!");
      var form = document.getElementById("addLocation");
      form.submit();

		}
	});

	$().ready(function() {
		// validate the comment form when it is submitted
		//$("#contact-form").validate();
        disabledDates = [];
        var location_id = $('#location-id').val();
        if(location_id == '')
        {
             disabledDates = [];
        }else
        {
            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
              $.ajax({
                  url: "{{ route('admin.host-location-avail')}}",
                  method: 'post',
                  data: {
                     location_id:location_id
                  },
                  success: function(result){
                     if(result['success'] == '1')
                     {
                         
                        
                         disabledDates = result['data'];
                     }
                  }});
           
        }
         
            $('#calendar').datepicker({
                inline:true,
                firstDay: 1,
                showOtherMonths:true,
                dayNamesMin:['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                beforeShowDay: function(date){
                    var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                    return [ disabledDates.indexOf(string) == -1 ];
                }
            });
		// validate signup form on keyup and submit
		$("#addLocation").validate({
			rules: {
                location_name: "required",
                max_rv_lenght: "required",
				files: "required",
				city: "required",
				state: "required",
				country: "required",
				zipcode: "required",
				space: "required",
				size: "required",
				stay: "required",
				tow_parking_available: "required",
                pull_through_parking_available: "required",
                general_description: "required",
                house_rules: "required",
                max_stay: "required",
                price_per_night: "required",

			},
			messages: {
				files: "Please select atleast one image."
			}
		});
	});
    
</script>	
@endsection