@extends('superadmin.layer')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>{{$user->user_fn}}({{$user->id}}) Location</h3>
                        
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
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                            Image
                            </th>
                            <th>
                            Location Name
                            </th>
                            <th>
                            Address
                            </th>
                            <th>
                                More Details
                            </th>
                            <th>
                            Action
                            </th>

                        </tr>
                        </thead>
                        <tbody>
                        @if(!$locations->isEmpty())
                            @foreach( $locations as $location)
                                <tr>
                                    <td>
                                        <img style="width:250px:border-round:none" src="{{URL::to('/')}}/public/img/locations/{{$location->id}}-1.jpg" alt="image">
                                        
                                    </td>
                                    <td>
                                    {{strtoupper($location->location_name)}}
                                    </td>
                                    
                                    <td>
                                        {{$location->address}}
                                        <br>
                                        {{$location->city}},{{$location->state}},{{$location->country}}
                                    </td>
                                    <td>
                                     <a href="{{route('superadmin.location-detail',$location->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    </td>
                                    <td>
                                        <form onSubmit="return confirm('are you sure want to delete this location once you delete this user can not able to book  ?')" class="delete-form" method="post" id="form{{$location->id}}" action="{{ route('superadmin.location-delete',$location->id)}}" style="float:left;  margin-left: 2px;"  >
                                            @csrf
                                            <input type="hidden" name="id" value="{{$location->id}}">
                                        </form>
                                        <button style="" type="submit" class="btn btn-warning btn-xs delete" title="delete" form="form{{$location->id}}">  <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection