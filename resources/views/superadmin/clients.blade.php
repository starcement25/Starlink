@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Users</h3> 
                </div>      
            </div>
        </div>
    </div>
       <!---->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <a href="{{route('superadmin.add-client')}}" class="btn btn-success">Add New Client</a>
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
                             Profile
                            </th>
                            <th>
                             Client
                            </th>
                            <th>
                            About
                            </th>
                            <th>
                            Say
                            </th>
                            <th>
                            Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!$clients->isEmpty())
                            @foreach( $clients as $client)
                                <tr>
                                    <td class="py-1">
                                        <img src="{{URL::to('/')}}/public/img/client/{{$client->img}}" alt="image">
                                    </td>
                                    <td>
                                    {{strtoupper($client->name)}}
                                    </td>
                                    <td>
                                       {{$client->about}}
                                    </td>
                                    <td>
                                       {{$client->say}}
                                    </td>
                                    <td>
                                        <form onSubmit="return confirm('are you sure want to delete?')" class="delete-form" method="post" id="form{{$client->id}}" action="{{route('superadmin.delete-client')}}" style="float:left;  margin-left: 2px;"  >
                                            @csrf
                                            <input type="hidden" name="id" value="{{$client->id}}">
                                        </form>
                                        <button style="" type="submit" class="btn btn-warning btn-xs delete" title="delete" form="form{{$client->id}}">  <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                        <a href="{{route('superadmin.update-client',$client->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-edit" aria-hidden="true"></i></a>
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