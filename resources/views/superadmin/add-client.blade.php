@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Add  Client</h3>
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
   
    <form class="user" id="create-client-form" method="post" action="{{route('superadmin.save-client')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="client_id" value="{{$client_id ?? ''}}">
            <div class="form-group">
                <input type="text" class="form-control "  name="name"   placeholder="Client Name" value="{{$client->name ?? ''}}">
            </div>
            <div class="form-group">
                <input type="text" class="form-control " id="" name="about"   placeholder="About Client" value="{{$client->about ?? ''}}">
            </div>
            <div class="form-group">
                <textarea type="text" class="form-control " id="" name="say"   placeholder="Client Say" >{{$client->say ?? ''}}</textarea>
            </div>
            <div class="form-group">
                <img id="preview-image-before-upload" src="{{URL::to('/')}}/public/img/client/{{$client->img ?? ''}}" alt="preview image" style="width:200px">
                <input type="file" name="image" placeholder="Choose image" id="image">
            </div> 
            <div class="form-group">
                @if($client_id != '')
                    <button type="submit" class="btn btn-success">Update Client</button>
                @else
                    <button type="submit" class="btn btn-success">Create Client</button>
                @endif
                
            </div>   
    </form>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $.validator.setDefaults({
      submitHandler: function() {
        //alert("submitted!");
        var form = document.getElementById("create-client-form");
        form.submit();
      }
    });
    
    $().ready(function() {
        $('#image').change(function() {
            alert('hellosd');
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#preview-image-before-upload').attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);
        });
      $("#create-client-form").validate({
        rules: {
          name: "required",
          about: 'required',
          client_say:"required",
        },
        messages: {
          name: "Please enter your name"				
         
        }
      });
	});
  </script>
@endsection