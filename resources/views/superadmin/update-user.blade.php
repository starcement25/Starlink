@extends('superadmin.layer')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Update {{($user->role == 2)? 'Host':''}} User</h3>
                </div>      
            </div>
        </div>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session()->get('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="col-lg-12 grid-margin stretch-card">
   
    <form class="user" id="create-user-form" method="post" action="{{route('superadmin.save-user')}}">
            @csrf
            <div class="form-group">
                <input type="text" class="form-control " id="username" name="username"   placeholder=" Name" value="{{$user->user_fn ?? ''}}">
                <input type="hidden" value="{{$user->id}}" name="user_id">
            </div>
            <div class="form-group">
                <input type="email" class="form-control " id="email" name="email"   placeholder="Email" value="{{$user->email ?? ''}}">
            </div>
            <div class="form-group">
                <input type="text" class="form-control " id="mobile" name="mobile"   placeholder="Mobile" value="{{$user->mobile ?? ''}}">
            </div>
            <div class="form-group">
                <select class="form-control"  name="usertype">
                    <option value="1" {{($user->role == 1)? 'selected':''}}>Users</option>
                    <option value="2" {{($user->role == 2)? 'selected':''}}>Host</option>
                </select>
            </div> 
            <div class="form-group">
                <button type="submit" class="btn btn-success">Upadate User</button>
            </div>   
    </form>
    </div>
</div>
@endsection
@section('js')
<script>
    $.validator.setDefaults({
      submitHandler: function() {
        //alert("submitted!");
        var form = document.getElementById("create-user-form");
        form.submit();
      }
    });

    $().ready(function() {
      $("#create-user-form").validate({
        rules: {
          username: "required",
          mobile: 'required',
          usertype:"required",
          email: {
            required: true,
            email: true
          },
        },
        messages: {
          username: "Please enter your name"				
         
        }
      });
	});
  </script>
@endsection