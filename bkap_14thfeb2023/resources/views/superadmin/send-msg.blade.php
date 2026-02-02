@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Message</h3>
                        
                </div>      
            </div>
        </div>
    </div>
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session()->get('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="col-lg-12 grid-margin stretch-card">
   
        <form action="{{route('superadmin.sending-msg')}}" method="post" id="msg-form">
                @csrf
                <div class="form-group form-check">
                    <label for="">Subject</label>
                    <input class="form-control" name="subject" required>
                </div>
                <div class="form-group form-check">
                    <label for="">message</label>
                    <br>
                    <textarea class="" name="msg" id="msg" required rows="10">
                    </textarea>
                </div>
                <div class="form-group">
                    <label for="Mobile">Mobile <input id="Mobile" type="checkbox" name="mobile" value="1"></label>
                    <label for="Email">Email <input type="checkbox" id="Email" name="email" value="1"></label>
                </div>
                
                <div class="form-group">
                    <label for="exampleInputEmail1">User Type</label>
                    <select class="form-control" id="usertype" required>
                        <option value="1">User</option>
                        <option value="2">Host</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="">User</label>
                    <select class="form-control" name="users[]" id="user" required multiple>
                        <option value="">Choose User</option>
                        
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">send</button>
        </form>
    </div>
    <div>
    <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        Message
                    </th>
                    <th>
                        Subject
                    </th>
                    <th>
                         No of User to sent
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
            @if(!$msgs->isEmpty())
                @foreach( $msgs as $msg)
                    <tr>
                        
                        <td>
                            {{$msg->msg}}
                        </td>
                        <td>
                            {{$msg->subject}}
                        </td>
                        <td>
                            {{$msg->users->count()}}
                        </td>
                        <td>
                            <form onSubmit="return confirm('are you sure want to delete?')" class="delete-form" method="post" id="form{{$msg->id}}" action="{{route('superadmin.delete-msg')}}" style="float:left;  margin-left: 2px;"  >
                                @csrf
                                <input type="hidden" name="msg_id" value="{{$msg->id}}">
                            </form>
                            <button style="" type="submit" class="btn btn-warning btn-xs delete" title="delete" form="form{{$msg->id}}">  <i class="fa fa-trash" aria-hidden="true"></i> </button>
                            
                        </td>
                    </tr>
                @endforeach
            @endif
            
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('js')

<script>
    $(document).ready(function() {
        $('#usertype').change(function(){
            var usertype = $(this).val();
            
            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
              $.ajax({
                  url: "{{ route('superadmin.userlist')}}",
                  method: 'post',
                  data: {
                     usertype:usertype
                  },
                  success: function(result){
                     if(result['success'] == '1')
                     {
                        var users = result['data'];
                        var option = "";
                        for (let i = 0; i < users.length; i++) {
                            option += "<option value='"+users[i]['id']+"'>"+users[i]['user_fn'] + "</option>";
                        }
                        $('#user').html(option);
                     }
                  }});
        });
		
		$("#msg-form").validate({
			rules: {
                usertype: "required",
                users: "required",
				msg: "required",
				subject: "required"	
			}
		});	
	});
</script>
@endsection