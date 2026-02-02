@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Host Users</h3>
                        
                </div>      
            </div>
        </div>
    </div>
       <!---->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                
                <a href="{{route('superadmin.add-user',2)}}" class="btn btn-success">Add New Host</a>
             
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
                            User
                            </th>
                            <th>
                            First name
                            </th>
                            <th>
                            Status
                            </th>
                            <th>
                            View Location
                            </th>
                            <th>
                            Action
                            </th>

                        </tr>
                        </thead>
                        <tbody>
                        @if(!$users->isEmpty())
                            @foreach( $users as $user)
                                <tr>
                                    <td class="py-1">
                                    @if($user->login_type == 'google')
                                    <img src="{{$user->profile_pic}}" alt="image">
                                    @else
                                        <img src="{{URL::to('/')}}/public/users/{{$user->profile_pic}}" alt="image">
                                    @endif
                                    
                                    </td>
                                    <td>
                                    {{strtoupper($user->user_fn)}}
                                    </td>
                                    
                                    <td>
                                        @if($user->active==0)
                                            <form id="deleteForm"   method="post" action="{{route('superadmin.user-status')}}">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-xs">Unblock</button>
                                                <input type="hidden" name="id" id="id" value="{{$user->id}}">
                                                <input type="hidden" name="status" id="status" value="{{$user->active}}">
                                            </form>
                                        @else
                                        <form id="deleteForm"   method="post" action="{{route('superadmin.user-status')}}">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-xs">Block</button>
                                                <input type="hidden" name="id" id="id" value="{{$user->id}}">
                                                <input type="hidden" name="status" id="status" value="{{$user->active}}">
                                        </form>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('superadmin.host-location',$user->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    </td>
                                    <td>
                                        <form onSubmit="return confirm('are you sure want to delete?')" class="delete-form" method="post" id="form{{$user->id}}" action="{{route('superadmin.delete-user')}}" style="float:left;  margin-left: 2px;"  >
                                            @csrf
                                            <input type="hidden" name="id" value="{{$user->id}}">
                                        </form>
                                        <button style="" type="submit" class="btn btn-warning btn-xs delete" title="delete" form="form{{$user->id}}">  <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                        <a href="{{route('superadmin.update-user',$user->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-edit" aria-hidden="true"></i></a>
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